<?php

namespace App\Services\Audit;

use App\Models\PenutupLhaRekomendasi;
use App\Models\EmailNotificationLog;
use App\Models\Audit\PerencanaanAudit;
use App\Models\Audit\PelaporanTemuan;
use App\Models\MasterData\MasterAuditee;
use App\Mail\ReminderRekomendasiMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonitoringService
{
    /**
     * Get planning list filtered by search and role access.
     */
    public function getSelectNomorSuratTugasList(?int $userAreaId, ?string $search, ?string $jenisAudit): array
    {
        // Single JOIN query replaces get() + map() with N+1 count sub-query
        $query = DB::table('perencanaan_audit as pa')
            ->join('pelaporan_hasil_audit as pha', 'pa.id', '=', 'pha.perencanaan_audit_id')
            ->join('pelaporan_temuan as pt', 'pha.id', '=', 'pt.pelaporan_hasil_audit_id')
            ->join('penutup_lha_rekomendasi as plr', 'pt.id', '=', 'plr.pelaporan_isi_lha_id')
            ->select(
                'pa.id as perencanaan_audit_id',
                'pa.nomor_surat_tugas',
                'pa.jenis_audit',
                DB::raw('COUNT(DISTINCT plr.id) as count_rekomendasi'),
                DB::raw('GROUP_CONCAT(DISTINCT pha.nomor_lha_lhk SEPARATOR ", ") as nomor_lha_lhk')
            )
            ->groupBy('pa.id', 'pa.nomor_surat_tugas', 'pa.jenis_audit')
            ->having('count_rekomendasi', '>', 0);

        if ($userAreaId) {
            $query->where('pa.area_id', $userAreaId);
        }

        if ($jenisAudit) {
            $query->where('pa.jenis_audit', $jenisAudit);
        }

        if ($search) {
            $escapedSearch = \App\Helpers\QueryHelper::escapeLike($search);
            $query->where(function ($q) use ($escapedSearch) {
                $q->where('pa.nomor_surat_tugas', 'like', '%' . $escapedSearch . '%')
                  ->orWhere('pha.nomor_lha_lhk', 'like', '%' . $escapedSearch . '%');
            });
        }

        $nomorSuratTugasList = $query->orderBy('pa.nomor_surat_tugas')->get()
            ->map(function ($row) {
                return [
                    'nomor_surat_tugas'    => $row->nomor_surat_tugas,
                    'perencanaan_audit_id' => $row->perencanaan_audit_id,
                    'jenis_audit'          => $row->jenis_audit,
                    'nomor_lha_lhk'        => $row->nomor_lha_lhk ?? '',
                    'count_rekomendasi'    => (int) $row->count_rekomendasi,
                ];
            })
            ->values();

        // Get audit types
        $jenisAuditQuery = DB::table('perencanaan_audit as pa')
            ->join('pelaporan_hasil_audit as pha', 'pa.id', '=', 'pha.perencanaan_audit_id')
            ->join('pelaporan_temuan as pt', 'pha.id', '=', 'pt.pelaporan_hasil_audit_id')
            ->join('penutup_lha_rekomendasi as plr', 'pt.id', '=', 'plr.pelaporan_isi_lha_id');

        if ($userAreaId) {
            $jenisAuditQuery->where('pa.area_id', $userAreaId);
        }

        $jenisAuditList = $jenisAuditQuery
            ->distinct()
            ->orderBy('pa.jenis_audit')
            ->pluck('pa.jenis_audit')
            ->values();

        return compact('nomorSuratTugasList', 'jenisAuditList');
    }

    /**
     * Get pemantauan data.
     */
    public function getPemantauanData(?string $nomorSuratTugas, ?int $userAreaId, ?string $bulan): array
    {
        $query = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'tindakLanjut',
            'picUsers'
        ]);
        
        if ($nomorSuratTugas) {
            $query->whereHas('temuan.pelaporanHasilAudit.perencanaanAudit', function($q) use ($nomorSuratTugas) {
                $q->where('nomor_surat_tugas', $nomorSuratTugas);
            });
        }
        
        if ($userAreaId) {
            $query->whereHas('temuan.pelaporanHasilAudit.perencanaanAudit', function($q) use ($userAreaId) {
                $q->where('area_id', $userAreaId);
            });
        }
        
        if ($bulan) {
            [$year, $month] = explode('-', $bulan);
            $query->whereYear('target_waktu', $year)->whereMonth('target_waktu', $month);
        }
        
        $data = $query->get();
        
        $perencanaanAudit = null;
        if ($nomorSuratTugas) {
            $perencanaanAudit = PerencanaanAudit::where('nomor_surat_tugas', $nomorSuratTugas)->first();
        }
        
        return compact('data', 'perencanaanAudit');
    }

    /**
     * Send email reminder.
     */
    public function sendReminder(PenutupLhaRekomendasi $rekomendasi, $currentUser): array
    {
        $pics = $rekomendasi->picUsers()->whereNotNull('email')->get();

        if ($pics->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada PIC dengan email terdaftar untuk rekomendasi ini.'
            ];
        }

        $sentTo  = [];
        $failed  = [];

        foreach ($pics as $pic) {
            try {
                Mail::to($pic->email, $pic->nama)
                    ->send(new ReminderRekomendasiMail($rekomendasi, $pic, 'manual'));

                EmailNotificationLog::create([
                    'penutup_lha_rekomendasi_id' => $rekomendasi->id,
                    'master_user_id'             => $pic->id,
                    'trigger_type'               => 'manual',
                    'sent_by'                    => $currentUser->id,
                    'status'                     => 'sent',
                    'sent_at'                    => now(),
                ]);

                $sentTo[] = $pic->nama;
            } catch (\Throwable $e) {
                EmailNotificationLog::create([
                    'penutup_lha_rekomendasi_id' => $rekomendasi->id,
                    'master_user_id'             => $pic->id,
                    'trigger_type'               => 'manual',
                    'sent_by'                    => $currentUser->id,
                    'status'                     => 'failed',
                    'error_message'              => $e->getMessage(),
                    'sent_at'                    => now(),
                ]);

                $failed[] = $pic->nama;
            }
        }

        $rekomendasi->update(['last_notified_at' => now()]);

        if (!empty($sentTo) && empty($failed)) {
            return [
                'success' => true,
                'message' => 'Email pengingat berhasil dikirim ke ' . count($sentTo) . ' PIC.',
                'sent_to' => $sentTo,
            ];
        } elseif (!empty($sentTo) && !empty($failed)) {
            return [
                'success' => true,
                'message' => 'Sebagian email terkirim. Berhasil: ' . count($sentTo) . ', Gagal: ' . count($failed),
                'sent_to' => $sentTo,
                'failed'  => $failed,
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Semua email gagal terkirim. Silakan periksa konfigurasi mail.',
                'failed'  => $failed,
            ];
        }
    }

    /**
     * Get aggregated monitoring data.
     * Fixes N+1 issue by using memory-level collections.
     */
    public function getMonitoringData(int $selectedYear, ?int $userAreaId): array
    {
        $currentMonth = Carbon::create($selectedYear, Carbon::now()->month, 1);
        $currentMonthName = Carbon::now()->format('M');

        // 1. Get per-auditee summary in a single aggregate query (replaces 3× get() + nested filter)
        $summaryQuery = DB::table('master_auditee as ma')
            ->join('perencanaan_audit as pa', 'pa.auditee_id', '=', 'ma.id')
            ->join('pelaporan_hasil_audit as pha', function ($join) {
                $join->on('pha.perencanaan_audit_id', '=', 'pa.id')
                     ->where('pha.status_approval', '=', 'approved');
            })
            ->leftJoin('pelaporan_temuan as pt', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->leftJoin('penutup_lha_rekomendasi as plr', 'plr.pelaporan_isi_lha_id', '=', 'pt.id')
            ->select(
                'ma.id as auditee_id',
                'ma.nama_bidang as divisi',
                DB::raw('COUNT(DISTINCT pt.id) as aoi'),
                DB::raw('COUNT(DISTINCT plr.id) as rekom'),
                DB::raw('COUNT(DISTINCT CASE WHEN plr.status_tindak_lanjut = "closed" THEN plr.id END) as tl_real')
            )
            ->groupBy('ma.id', 'ma.nama_bidang');

        if ($userAreaId !== null) {
            $summaryQuery
                ->join('perencanaan_audit as pa_filter', 'pa_filter.auditee_id', '=', 'ma.id')
                ->where('pa_filter.area_id', $userAreaId);
        }

        $summaryRows = $summaryQuery->get();

        // 2. Get monthly target/real counts in a single query per auditee
        $auditeeIds = $summaryRows->pluck('auditee_id')->toArray();

        $monthlyTarget = collect();
        $monthlyReal = collect();

        if (!empty($auditeeIds)) {
            $monthlyTarget = DB::table('penutup_lha_rekomendasi as plr')
                ->join('pelaporan_temuan as pt', 'plr.pelaporan_isi_lha_id', '=', 'pt.id')
                ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
                ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
                ->whereIn('pa.auditee_id', $auditeeIds)
                ->whereNotNull('plr.target_waktu')
                ->whereYear('plr.target_waktu', $selectedYear)
                ->select(
                    'pa.auditee_id',
                    DB::raw('MONTH(plr.target_waktu) as bulan'),
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('pa.auditee_id', DB::raw('MONTH(plr.target_waktu)'))
                ->get()
                ->groupBy('auditee_id');

            $monthlyReal = DB::table('penutup_lha_rekomendasi as plr')
                ->join('pelaporan_temuan as pt', 'plr.pelaporan_isi_lha_id', '=', 'pt.id')
                ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
                ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
                ->whereIn('pa.auditee_id', $auditeeIds)
                ->where('plr.status_tindak_lanjut', 'closed')
                ->whereNotNull('plr.real_waktu')
                ->whereYear('plr.real_waktu', $selectedYear)
                ->select(
                    'pa.auditee_id',
                    DB::raw('MONTH(plr.real_waktu) as bulan'),
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('pa.auditee_id', DB::raw('MONTH(plr.real_waktu)'))
                ->get()
                ->groupBy('auditee_id');
        }

        $monthsMapping = [
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'mei' => 5, 'jun' => 6,
            'jul' => 7, 'ags' => 8, 'sep' => 9, 'okt' => 10, 'nov' => 11, 'des' => 12
        ];

        $auditeeData = [];
        $no = 1;

        foreach ($summaryRows as $row) {
            $aoiCount = (int) $row->aoi;
            $rekomCount = (int) $row->rekom;
            $tindakLanjutReal = (int) $row->tl_real;
            $tindakLanjutTarget = $rekomCount;
            $sisaTarget = $tindakLanjutTarget - $tindakLanjutReal;

            // Build monthly data from pre-fetched aggregates
            $targetForAuditee = $monthlyTarget->get($row->auditee_id, collect());
            $realForAuditee = $monthlyReal->get($row->auditee_id, collect());

            $bulanan = [];
            foreach ($monthsMapping as $monthKey => $monthNumber) {
                $target = $targetForAuditee->firstWhere('bulan', $monthNumber)->total ?? 0;
                $real = $realForAuditee->firstWhere('bulan', $monthNumber)->total ?? 0;
                $bulanan[$monthKey] = ['target' => (int) $target, 'real' => (int) $real];
            }

            // Semesters
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
                'objek_pemeriksaan' => $row->divisi,
                'aoi' => $aoiCount,
                'rekom' => $rekomCount,
                'tindak_lanjut_target' => $tindakLanjutTarget,
                'tindak_lanjut_real' => $tindakLanjutReal,
                'sisa_target' => $sisaTarget,
                'sisa_real' => 0,
                'bulanan' => $bulanan,
                'semester' => [
                    'smt1' => ['target' => $smt1Target, 'real' => $smt1Real],
                    'smt2' => ['target' => $smt2Target, 'real' => $smt2Real],
                ],
                'is_empty' => $aoiCount == 0 && $rekomCount == 0
            ];
        }

        $totalData = $this->calculateTotalData($auditeeData);
        $realisasiKumulatif = $this->calculateRealisasiKumulatif($totalData);
        $semesterData = $this->calculateSemesterData($totalData);
        $years = range(Carbon::now()->year - 2, Carbon::now()->year + 1);

        return compact(
            'auditeeData', 
            'totalData', 
            'realisasiKumulatif', 
            'currentMonthName',
            'semesterData',
            'selectedYear',
            'years'
        );
    }


    private function calculateTotalData(array $auditeeData): array
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

    private function calculateRealisasiKumulatif(array $totalData): string
    {
        $totalTarget = $totalData['tindak_lanjut_target'] ?? 0;
        $totalReal = $totalData['tindak_lanjut_real'] ?? 0;
        
        if ($totalTarget > 0) {
            $percentage = ($totalReal / $totalTarget) * 100;
            return floor($percentage) == $percentage ? number_format($percentage, 0) : number_format($percentage, 1);
        }
        
        return '0';
    }

    private function calculateSemesterData(array $totalData): array
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

    /**
     * Get progress dashboard data.
     * Fixes N+1 issue by using grouping and raw aggregations.
     */
    public function getProgressData(int $selectedYear, string $selectedStatus, ?int $selectedAuditee, ?int $userAreaId): array
    {
        // 1. Base Query for Recommendation Data table
        $query = PenutupLhaRekomendasi::with([
            'temuan.pelaporanHasilAudit.perencanaanAudit.auditee',
            'tindakLanjut'
        ]);
        
        if ($selectedAuditee) {
            $query->whereHas('temuan.pelaporanHasilAudit.perencanaanAudit', function($q) use ($selectedAuditee) {
                $q->where('auditee_id', $selectedAuditee);
            });
        }
        
        if ($userAreaId !== null) {
            $query->whereHas('temuan.pelaporanHasilAudit.perencanaanAudit', function($q) use ($userAreaId) {
                $q->where('area_id', $userAreaId);
            });
        }
        
        if ($selectedStatus !== 'all') {
            $query->where('status_tindak_lanjut', $selectedStatus);
        }
        
        $rekomendasiData = $query->get();

        // 2. Summary Counts (Optimized: 1 query instead of 4)
        $summaryQuery = PenutupLhaRekomendasi::query();
        if ($userAreaId !== null) {
            $summaryQuery->whereHas('temuan.pelaporanHasilAudit.perencanaanAudit', function($q) use ($userAreaId) {
                $q->where('area_id', $userAreaId);
            });
        }
        $stats = $summaryQuery->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status_tindak_lanjut = 'open' THEN 1 ELSE 0 END) as open_count,
            SUM(CASE WHEN status_tindak_lanjut = 'on_progress' THEN 1 ELSE 0 END) as on_progress_count,
            SUM(CASE WHEN status_tindak_lanjut = 'closed' THEN 1 ELSE 0 END) as closed_count
        ")->first();

        $totalRekomendasi = $stats->total ?? 0;
        $statusOpen = $stats->open_count ?? 0;
        $statusOnProgress = $stats->on_progress_count ?? 0;
        $statusClosed = $stats->closed_count ?? 0;
        
        $statusData = [
            'Open' => $statusOpen,
            'On Progress' => $statusOnProgress,
            'Closed' => $statusClosed
        ];

        // 3. Progress per Auditee (Top 10) (Optimized: query relationships instead of loading everything)
        $auditeeProgressQuery = PenutupLhaRekomendasi::with('temuan.pelaporanHasilAudit.perencanaanAudit.auditee');
        if ($userAreaId !== null) {
            $auditeeProgressQuery->whereHas('temuan.pelaporanHasilAudit.perencanaanAudit', function($q) use ($userAreaId) {
                $q->where('area_id', $userAreaId);
            });
        }
        $auditeeProgress = $auditeeProgressQuery->get()
            ->groupBy(function($item) {
                $auditee = $item->temuan->pelaporanHasilAudit->perencanaanAudit->auditee ?? null;
                if ($auditee) {
                    $direktorat = $auditee->direktorat ?? '';
                    $divisiCabang = $auditee->divisi_cabang ?? '';
                    $divisi = $auditee->divisi ?? '';
                    
                    if (!empty($direktorat) || !empty($divisiCabang)) {
                        return trim(trim(($direktorat ?? '') . ' - ' . ($divisiCabang ?? '')));
                    } elseif (!empty($divisi)) {
                        return $divisi;
                    }
                }
                return 'Unknown';
            })
            ->map(function($group, $auditeeName) {
                return [
                    'name' => $auditeeName,
                    'total' => $group->count(),
                    'open' => $group->where('status_tindak_lanjut', 'open')->count(),
                    'on_progress' => $group->where('status_tindak_lanjut', 'on_progress')->count(),
                    'closed' => $group->where('status_tindak_lanjut', 'closed')->count(),
                ];
            })
            ->sortByDesc('total')
            ->take(10)
            ->values();

        // 4. Progress per Bulan (Optimized: 1 raw aggregate query instead of 36 queries)
        $bulananQuery = PenutupLhaRekomendasi::whereYear('created_at', $selectedYear);
        if ($selectedAuditee) {
            $bulananQuery->whereHas('temuan.pelaporanHasilAudit.perencanaanAudit', function($q) use ($selectedAuditee) {
                $q->where('auditee_id', $selectedAuditee);
            });
        }
        if ($userAreaId !== null) {
            $bulananQuery->whereHas('temuan.pelaporanHasilAudit.perencanaanAudit', function($q) use ($userAreaId) {
                $q->where('area_id', $userAreaId);
            });
        }
        $bulananRaw = $bulananQuery->selectRaw("
            MONTH(created_at) as month_num,
            status_tindak_lanjut,
            COUNT(*) as count
        ")
        ->groupBy('month_num', 'status_tindak_lanjut')
        ->get();

        $bulananData = [];
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create(null, $i, 1)->translatedFormat('M');
            $months[] = $monthName;
            
            $openForMonth = $bulananRaw->first(fn($b) => $b->month_num == $i && $b->status_tindak_lanjut == 'open')->count ?? 0;
            $onProgressForMonth = $bulananRaw->first(fn($b) => $b->month_num == $i && $b->status_tindak_lanjut == 'on_progress')->count ?? 0;
            $closedForMonth = $bulananRaw->first(fn($b) => $b->month_num == $i && $b->status_tindak_lanjut == 'closed')->count ?? 0;

            $bulananData[$monthName] = [
                'open' => $openForMonth,
                'on_progress' => $onProgressForMonth,
                'closed' => $closedForMonth,
            ];
        }

        // 5. Completion Rate
        $completionRate = $totalRekomendasi > 0 
            ? round(($statusClosed / $totalRekomendasi) * 100, 2) 
            : 0;

        // 6. On Time vs Overdue
        $onTimeCount = 0;
        $overdueCount = 0;
        
        foreach ($rekomendasiData as $rekomendasi) {
            if ($rekomendasi->target_waktu && $rekomendasi->real_waktu) {
                $targetDate = Carbon::parse($rekomendasi->target_waktu);
                $realDate = Carbon::parse($rekomendasi->real_waktu);
                
                if ($realDate->lte($targetDate)) {
                    $onTimeCount++;
                } else {
                    $overdueCount++;
                }
            } elseif ($rekomendasi->target_waktu) {
                $targetDate = Carbon::parse($rekomendasi->target_waktu);
                if (Carbon::now()->gt($targetDate) && $rekomendasi->status_tindak_lanjut != 'closed') {
                    $overdueCount++;
                }
            }
        }

        // 7. Detail Data untuk Tabel
        $detailData = $rekomendasiData->map(function($item) {
            $auditee = $item->temuan->pelaporanHasilAudit->perencanaanAudit->auditee ?? null;
            $auditeeName = 'Unknown';
            
            if ($auditee) {
                $direktorat = $auditee->direktorat ?? '';
                $divisiCabang = $auditee->divisi_cabang ?? '';
                $divisi = $auditee->divisi ?? '';
                
                if (!empty($direktorat) || !empty($divisiCabang)) {
                    $auditeeName = trim(trim(($direktorat ?? '') . ' - ' . ($divisiCabang ?? '')));
                    $auditeeName = trim($auditeeName, '- ');
                } elseif (!empty($divisi)) {
                    $auditeeName = $divisi;
                }
            }
            
            $latestTindakLanjut = $item->tindakLanjut->sortByDesc('created_at')->first();
            $progressPercentage = 0;
            
            if ($item->status_tindak_lanjut == 'closed') {
                $progressPercentage = 100;
            } elseif ($item->status_tindak_lanjut == 'on_progress') {
                $progressPercentage = 50;
            } else {
                $progressPercentage = 0;
            }
            
            return [
                'id' => $item->id,
                'rekomendasi' => $item->rekomendasi,
                'auditee' => $auditeeName,
                'target_waktu' => $item->target_waktu ? Carbon::parse($item->target_waktu)->format('d M Y') : '-',
                'real_waktu' => $item->real_waktu ? Carbon::parse($item->real_waktu)->format('d M Y') : '-',
                'status' => $item->status_tindak_lanjut,
                'progress' => $progressPercentage,
                'latest_update' => $latestTindakLanjut ? $latestTindakLanjut->created_at->format('d M Y') : '-',
            ];
        });

        $audites = MasterAuditee::all();

        return compact(
            'totalRekomendasi',
            'statusOpen',
            'statusOnProgress',
            'statusClosed',
            'statusData',
            'auditeeProgress',
            'bulananData',
            'months',
            'completionRate',
            'onTimeCount',
            'overdueCount',
            'detailData',
            'audites',
            'selectedYear',
            'selectedStatus',
            'selectedAuditee'
        );
    }
}
