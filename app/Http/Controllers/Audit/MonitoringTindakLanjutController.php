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
        // Ambil bulan sekarang
        $currentMonth = Carbon::now();
        $currentMonthName = $currentMonth->format('M'); // Jan, Feb, Mar, etc.
        
        // Ambil data auditee yang memiliki data audit
        $auditeeData = $this->getAuditeeData($currentMonth);
        
        // Hitung total untuk baris JUMLAH
        $totalData = $this->calculateTotalData($auditeeData);
        
        // Hitung realisasi kumulatif
        $realisasiKumulatif = $this->calculateRealisasiKumulatif($totalData);
        
        return view('audit.monitoring-tindak-lanjut.index', compact(
            'auditeeData', 
            'totalData', 
            'realisasiKumulatif', 
            'currentMonthName'
        ));
    }
    
    private function getAuditeeData($currentMonth)
    {
        // Ambil data auditee yang memiliki perencanaan audit
        $auditees = MasterAuditee::whereHas('perencanaanAudit', function($query) {
            $query->whereHas('pelaporanHasilAudit', function($q) {
                $q->where('status_approval', 'approved');
            });
        })->get();
        
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
            $tindakLanjutReal = PenutupLhaTindakLanjut::whereHas('rekomendasi', function($query) use ($auditee) {
                $query->whereHas('temuan', function($q) use ($auditee) {
                    $q->whereHas('pelaporanHasilAudit', function($q2) use ($auditee) {
                        $q2->whereHas('perencanaanAudit', function($q3) use ($auditee) {
                            $q3->where('auditee_id', $auditee->id);
                        });
                    });
                });
            })->count();
            
            // Hitung sisa
            $sisaTarget = $tindakLanjutTarget - $tindakLanjutReal;
            $sisaReal = 0; // Sisa real selalu 0 karena sudah ditindak lanjuti
            
            // Hitung data bulanan berdasarkan target waktu rekomendasi
            $bulanan = $this->calculateMonthlyData($auditee, $currentMonth);
            
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
            
            // Real: jumlah tindak lanjut yang dibuat di bulan tersebut
            $real = PenutupLhaTindakLanjut::whereHas('rekomendasi', function($query) use ($auditee, $monthNumber, $currentMonth) {
                $query->whereHas('temuan', function($q) use ($auditee) {
                    $q->whereHas('pelaporanHasilAudit', function($q2) use ($auditee) {
                        $q2->whereHas('perencanaanAudit', function($q3) use ($auditee) {
                            $q3->where('auditee_id', $auditee->id);
                        });
                    });
                });
            })->whereMonth('created_at', $monthNumber)
              ->whereYear('created_at', $currentMonth->year)
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
    
    private function calculateRealisasiKumulatif($totalData)
    {
        $currentMonth = $this->getCurrentMonthNumber();
        $months = [
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'mei' => 5, 'jun' => 6,
            'jul' => 7, 'ags' => 8, 'sep' => 9, 'okt' => 10, 'nov' => 11, 'des' => 12
        ];
        
        $totalTarget = 0;
        $totalReal = 0;
        
        // Hanya hitung sampai bulan saat ini
        foreach ($months as $monthKey => $monthNumber) {
            if ($monthNumber <= $currentMonth) {
                $totalTarget += $totalData['bulanan'][$monthKey]['target'];
                $totalReal += $totalData['bulanan'][$monthKey]['real'];
            }
        }
        
        return $totalTarget > 0 ? round(($totalReal / $totalTarget) * 100) : 0;
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
