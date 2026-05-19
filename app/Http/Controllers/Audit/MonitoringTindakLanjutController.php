<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\PenutupLhaRekomendasi;
use App\Models\PenutupLhaTindakLanjut;
use App\Models\MonitoringTindakLanjut;
use App\Models\Audit\PelaporanHasilAudit;
use App\Models\Audit\PelaporanTemuan;
use App\Models\Audit\PerencanaanAudit;
use App\Models\MasterData\MasterAuditee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonitoringTindakLanjutController extends Controller
{
    public function index(Request $request)
    {
        // Tahun yang dipilih (default tahun ini)
        $selectedYear = $request->input('year', Carbon::now()->year);
        
        // Ambil bulan sekarang (untuk batas perhitungan berjalan jika tahun yang dipilih adalah tahun ini)
        $currentMonth = Carbon::create($selectedYear, Carbon::now()->month, 1);
        $currentMonthName = Carbon::now()->format('M'); // Jan, Feb, Mar, dll.
        
        // Ambil data auditee yang memiliki data audit
        $auditeeData = $this->getAuditeeData($currentMonth);
        
        // Hitung total untuk baris JUMLAH
        $totalData = $this->calculateTotalData($auditeeData);
        
        // Hitung realisasi kumulatif
        $realisasiKumulatif = $this->calculateRealisasiKumulatif($totalData, $selectedYear);
        
        // Hitung persentase semester
        $semesterData = $this->calculateSemesterData($totalData);
        
        // Daftar tahun untuk filter (bisa disesuaikan dengan range data aktual)
        $years = range(Carbon::now()->year - 2, Carbon::now()->year + 1);
        
        return view('audit.monitoring-tindak-lanjut.index', compact(
            'auditeeData', 
            'totalData', 
            'realisasiKumulatif', 
            'currentMonthName',
            'semesterData',
            'selectedYear',
            'years'
        ));
    }
    
    private function getAuditeeData($currentMonth)
    {
        $auditeeQuery = MasterAuditee::whereHas('perencanaanAudit', function($query) {
            $query->whereHas('pelaporanHasilAudit', function($q) {
                $q->where('status_approval', 'approved');
            });
        });
        
        if (\App\Helpers\AuthHelper::isAuditee()) {
            $userAuditeeId = \App\Helpers\AuthHelper::getUserAuditeeId();
            if ($userAuditeeId !== null) {
                $auditeeQuery->where('id', $userAuditeeId);
            }
        }
        
        $auditees = $auditeeQuery->get();
        
        $auditeeData = [];
        $no = 1;
        
        foreach ($auditees as $auditee) {
            // Hitung jumlah AOI (temuan) dari pelaporan hasil audit yang approved
            $aoiCount = PelaporanTemuan::whereHas('pelaporanHasilAudit', function($query) use ($auditee) {
                $query->whereHas('perencanaanAudit', function($q) use ($auditee) {
                    $q->where('auditee_id', $auditee->id);
                })->where('status_approval', 'approved');
            })->count();
            
            // Hitung jumlah rekomendasi dari penutup LHA rekomendasi
            $rekomCount = PenutupLhaRekomendasi::whereHas('temuan', function($query) use ($auditee) {
                $query->whereHas('pelaporanHasilAudit', function($q) use ($auditee) {
                    $q->whereHas('perencanaanAudit', function($q2) use ($auditee) {
                        $q2->where('auditee_id', $auditee->id);
                    });
                });
            })->count();
            
            // Hitung tindak lanjut target dan real
            $tindakLanjutTarget = $rekomCount; // Target = jumlah rekomendasi
            $tindakLanjutReal = PenutupLhaRekomendasi::where('status_tindak_lanjut', 'closed')
                ->whereHas('temuan', function($q) use ($auditee) {
                    $q->whereHas('pelaporanHasilAudit', function($q2) use ($auditee) {
                        $q2->whereHas('perencanaanAudit', function($q3) use ($auditee) {
                            $q3->where('auditee_id', $auditee->id);
                        });
                    });
                })->count();
            
            // Hitung sisa
            $sisaTarget = $tindakLanjutTarget - $tindakLanjutReal;
            $sisaReal = 0; // Sisa real selalu 0 karena sudah ditindak lanjuti
            
            // Hitung data bulanan berdasarkan target waktu rekomendasi
            $bulanan = $this->calculateMonthlyData($auditee, $currentMonth);
            
            // Hitung target dan real semester per auditee
            $smt1Target = 0; $smt1Real = 0;
            $smt2Target = 0; $smt2Real = 0;
            foreach (['jan', 'feb', 'mar', 'apr', 'mei', 'jun'] as $m) {
                $smt1Target += $bulanan[$m]['target'];
                $smt1Real += $bulanan[$m]['real'];
            }
            foreach (['jul', 'ags', 'sep', 'okt', 'nov', 'des'] as $m) {
                $smt2Target += $bulanan[$m]['target'];
                $smt2Real += $bulanan[$m]['real'];
            }
            
            $auditeeData[] = [
                'no' => $no++,
                'objek_pemeriksaan' => $auditee->divisi,
                'aoi' => $aoiCount,
                'rekom' => $rekomCount,
                'tindak_lanjut_target' => $tindakLanjutTarget,
                'tindak_lanjut_real' => $tindakLanjutReal,
                'sisa_target' => $sisaTarget,
                'sisa_real' => $sisaReal,
                'bulanan' => $bulanan,
                'semester' => [
                    'smt1' => ['target' => $smt1Target, 'real' => $smt1Real],
                    'smt2' => ['target' => $smt2Target, 'real' => $smt2Real],
                ],
                'is_empty' => $aoiCount == 0 && $rekomCount == 0
            ];
        }
        
        return $auditeeData;
    }
    
    private function calculateMonthlyData($auditee, $currentMonth)
    {
        $months = [
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'mei' => 5, 'jun' => 6,
            'jul' => 7, 'ags' => 8, 'sep' => 9, 'okt' => 10, 'nov' => 11, 'des' => 12
        ];
        
        $monthlyData = [];
        
        foreach ($months as $monthKey => $monthNumber) {
            // Target: jumlah rekomendasi dengan target waktu di bulan tersebut
            $target = PenutupLhaRekomendasi::whereHas('temuan', function($query) use ($auditee) {
                $query->whereHas('pelaporanHasilAudit', function($q) use ($auditee) {
                    $q->whereHas('perencanaanAudit', function($q2) use ($auditee) {
                        $q2->where('auditee_id', $auditee->id);
                    });
                });
            })->whereMonth('target_waktu', $monthNumber)
              ->whereYear('target_waktu', $currentMonth->year)
              ->count();
            
            // Real: jumlah rekomendasi yang statusnya closed di bulan tersebut
            $real = PenutupLhaRekomendasi::where('status_tindak_lanjut', 'closed')
              ->whereHas('temuan', function($query) use ($auditee) {
                  $query->whereHas('pelaporanHasilAudit', function($q) use ($auditee) {
                      $q->whereHas('perencanaanAudit', function($q2) use ($auditee) {
                          $q2->where('auditee_id', $auditee->id);
                      });
                  });
              })->whereMonth('real_waktu', $monthNumber)
                ->whereYear('real_waktu', $currentMonth->year)
                ->count();
            
            $monthlyData[$monthKey] = [
                'target' => $target,
                'real' => $real
            ];
        }
        
        return $monthlyData;
    }
    
    private function calculateTotalData($auditeeData)
    {
        $totalData = [
            'aoi' => 0,
            'rekom' => 0,
            'tindak_lanjut_target' => 0,
            'tindak_lanjut_real' => 0,
            'sisa_target' => 0,
            'sisa_real' => 0,
            'bulanan' => [
                'jan' => ['target' => 0, 'real' => 0],
                'feb' => ['target' => 0, 'real' => 0],
                'mar' => ['target' => 0, 'real' => 0],
                'apr' => ['target' => 0, 'real' => 0],
                'mei' => ['target' => 0, 'real' => 0],
                'jun' => ['target' => 0, 'real' => 0],
                'jul' => ['target' => 0, 'real' => 0],
                'ags' => ['target' => 0, 'real' => 0],
                'sep' => ['target' => 0, 'real' => 0],
                'okt' => ['target' => 0, 'real' => 0],
                'nov' => ['target' => 0, 'real' => 0],
                'des' => ['target' => 0, 'real' => 0],
            ]
        ];
        
        foreach ($auditeeData as $data) {
            if (!$data['is_empty']) {
                $totalData['aoi'] += $data['aoi'];
                $totalData['rekom'] += $data['rekom'];
                $totalData['tindak_lanjut_target'] += $data['tindak_lanjut_target'];
                $totalData['tindak_lanjut_real'] += $data['tindak_lanjut_real'];
                $totalData['sisa_target'] += $data['sisa_target'];
                $totalData['sisa_real'] += $data['sisa_real'];
                
                foreach ($data['bulanan'] as $month => $monthData) {
                    $totalData['bulanan'][$month]['target'] += $monthData['target'];
                    $totalData['bulanan'][$month]['real'] += $monthData['real'];
                }
            }
        }
        
        return $totalData;
    }
    
    private function calculateRealisasiKumulatif($totalData, $selectedYear)
    {
        $totalTarget = $totalData['tindak_lanjut_target'] ?? 0;
        $totalReal = $totalData['tindak_lanjut_real'] ?? 0;
        
        if ($totalTarget > 0) {
            $percentage = ($totalReal / $totalTarget) * 100;
            // Format 1 desimal jika ada pecahan, jika bulat maka tidak ada koma
            return floor($percentage) == $percentage ? number_format($percentage, 0) : number_format($percentage, 1);
        }
        
        return 0;
    }
    
    private function calculateSemesterData($totalData)
    {
        $smt1Target = 0; $smt1Real = 0;
        $smt2Target = 0; $smt2Real = 0;

        $smt1Months = ['jan', 'feb', 'mar', 'apr', 'mei', 'jun'];
        $smt2Months = ['jul', 'ags', 'sep', 'okt', 'nov', 'des'];

        foreach ($smt1Months as $month) {
            $smt1Target += $totalData['bulanan'][$month]['target'];
            $smt1Real += $totalData['bulanan'][$month]['real'];
        }

        foreach ($smt2Months as $month) {
            $smt2Target += $totalData['bulanan'][$month]['target'];
            $smt2Real += $totalData['bulanan'][$month]['real'];
        }

        $smt1Percentage = $smt1Target > 0 ? round(($smt1Real / $smt1Target) * 100) : 0;
        $smt2Percentage = $smt2Target > 0 ? round(($smt2Real / $smt2Target) * 100) : 0;

        return [
            'smt1' => [
                'target' => $smt1Target,
                'real' => $smt1Real,
                'percentage' => $smt1Percentage
            ],
            'smt2' => [
                'target' => $smt2Target,
                'real' => $smt2Real,
                'percentage' => $smt2Percentage
            ]
        ];
    }
    
    private function getCurrentMonthNumber()
    {
        return Carbon::now()->month;
    }
    
    private function shouldCalculatePercentage($monthNumber)
    {
        $currentMonth = $this->getCurrentMonthNumber();
        return $monthNumber <= $currentMonth;
    }
}
