<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Audit\PerencanaanAudit;
use App\Models\RealisasiAudit;
use App\Models\Audit\PelaporanTemuan;
use App\Models\PenutupLhaRekomendasi;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterKodeRisk;
use App\Models\MasterData\MasterUnit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardAnalitikController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $divisiId = $request->divisi_id;
        $unitId = $request->unit_id;

        $masterDivisi = MasterAuditee::select('id', 'divisi')->orderBy('divisi')->get();
        $masterUnit = MasterUnit::select('id', 'nama_unit')->orderBy('nama_unit')->get();

        // 1. KPI Summary Cards
        $queryPlan = PerencanaanAudit::query();
        if($startDate) $queryPlan->whereDate('tanggal_audit_mulai', '>=', $startDate);
        if($endDate) $queryPlan->whereDate('tanggal_audit_sampai', '<=', $endDate);
        if($divisiId) $queryPlan->where('auditee_id', $divisiId);
        if($unitId) $queryPlan->where('unit_id', $unitId);

        $totalDirencanakan = $queryPlan->count();
        $planIds = $queryPlan->pluck('id')->toArray();

        // Count via Entry Meeting -> PKA -> Perencanaan (correct join path)
        $emPlanIds = DB::table('entry_meeting')
            ->join('program_kerja_audit as pka', 'entry_meeting.program_kerja_audit_id', '=', 'pka.id')
            ->whereIn('pka.perencanaan_audit_id', $planIds)
            ->distinct()
            ->pluck('pka.perencanaan_audit_id');
        
        $totalFromEM = $emPlanIds->count();
        
        // Count fallback: Realisasi Audits that do NOT have an entry meeting record
        $totalFromFallback = DB::table('realisasi_audits')
            ->whereIn('perencanaan_audit_id', $planIds)
            ->whereNotIn('perencanaan_audit_id', $emPlanIds)
            ->distinct('perencanaan_audit_id')
            ->count('perencanaan_audit_id');
        
        $totalTerealisasi = $totalFromEM + $totalFromFallback;
            
        // Total Temuan
        $totalTemuan = DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->whereIn('pha.perencanaan_audit_id', $planIds)
            ->count('pt.id');
        
        // Rekomendasi
        $rekomendasiStatus = DB::table('penutup_lha_rekomendasi as plr')
            ->join('pelaporan_temuan as pt', 'plr.pelaporan_isi_lha_id', '=', 'pt.id')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->whereIn('pha.perencanaan_audit_id', $planIds)
            ->select('plr.status_tindak_lanjut', DB::raw('count(*) as total'))
            ->groupBy('plr.status_tindak_lanjut')
            ->get()
            ->pluck('total', 'status_tindak_lanjut')
            ->toArray();
            
        $rekomendasiOpen = $rekomendasiStatus['open'] ?? 0;
        $rekomendasiClosed = $rekomendasiStatus['closed'] ?? 0;
        $rekomendasiOnProgress = $rekomendasiStatus['on_progress'] ?? 0;
        
        $totalTl = array_sum($rekomendasiStatus);
        $percentClosed = $totalTl > 0 ? round(($rekomendasiClosed / $totalTl) * 100, 1) : 0;
        
        // 2. Data for Section 1: Tren Penyelesaian Audit (Bulanan)
        $trenBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $trenSelesai = array_fill(0, 12, 0);

        $realisasiSelesai = RealisasiAudit::whereIn('perencanaan_audit_id', $planIds)
            ->where('status', 'selesai')
            ->whereNotNull('tanggal_selesai')
            ->get();

        foreach($realisasiSelesai as $r) {
            $monthIndex = Carbon::parse($r->tanggal_selesai)->month - 1; // 0-11
            $trenSelesai[$monthIndex]++;
        }
        
        // 3. Data for Section 1: Aging Keterlambatan Tindak Lanjut
        $agingCounts = [
            'Sesuai Target' => 0,
            '< 30 Hari' => 0,
            '31-60 Hari' => 0,
            '61-90 Hari' => 0,
            '> 90 Hari' => 0
        ];
        
        // Detail per bucket untuk tooltip yang informatif
        $agingDetails = [
            'Sesuai Target' => [],
            '< 30 Hari' => [],
            '31-60 Hari' => [],
            '61-90 Hari' => [],
            '> 90 Hari' => [],
        ];

        $openTL = DB::table('penutup_lha_rekomendasi as plr')
            ->join('pelaporan_temuan as pt', 'plr.pelaporan_isi_lha_id', '=', 'pt.id')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->leftJoin('master_unit as mu', 'pa.unit_id', '=', 'mu.id')
            ->whereIn('pha.perencanaan_audit_id', $planIds)
            ->where(function($q) {
                $q->whereIn('plr.status_tindak_lanjut', ['open', 'on_progress'])
                  ->orWhereNull('plr.status_tindak_lanjut');
            })
            ->whereNotNull('plr.target_waktu')
            ->select(
                'plr.target_waktu',
                'plr.status_tindak_lanjut',
                'plr.rekomendasi',
                'ma.divisi',
                'mu.nama_unit as unit'
            )
            ->get();

        $now = Carbon::now();
        foreach($openTL as $tl) {
            $target = Carbon::parse($tl->target_waktu);
            $daysLate = $target >= $now ? 0 : (int) $target->diffInDays($now);
            $detail = [
                'divisi'    => $tl->divisi ?? '-',
                'unit'      => $tl->unit ?? '-',
                'days_late' => $daysLate,
            ];
            
            if ($target >= $now) {
                $agingCounts['Sesuai Target']++;
                $agingDetails['Sesuai Target'][] = $detail;
            } else {
                if ($daysLate <= 30) {
                    $agingCounts['< 30 Hari']++;
                    $agingDetails['< 30 Hari'][] = $detail;
                } elseif ($daysLate <= 60) {
                    $agingCounts['31-60 Hari']++;
                    $agingDetails['31-60 Hari'][] = $detail;
                } elseif ($daysLate <= 90) {
                    $agingCounts['61-90 Hari']++;
                    $agingDetails['61-90 Hari'][] = $detail;
                } else {
                    $agingCounts['> 90 Hari']++;
                    $agingDetails['> 90 Hari'][] = $detail;
                }
            }
        }

        $agingCategories = array_keys($agingCounts);
        $agingData = array_values($agingCounts);
        // Limit detail per bucket to top 5 (sorted by most overdue first) for tooltip
        foreach ($agingDetails as $bucket => $items) {
            usort($agingDetails[$bucket], fn($a, $b) => $b['days_late'] - $a['days_late']);
            $agingDetails[$bucket] = array_slice($agingDetails[$bucket], 0, 5);
        }
        
        // Status Realisasi
        $statusCounts = RealisasiAudit::whereIn('perencanaan_audit_id', $planIds)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // 3. Data for Section 2: Temuan vs Tindak Lanjut (Stacked Bar)
        $temuanTl = DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->leftJoin('penutup_lha_rekomendasi as plr', 'pt.id', '=', 'plr.pelaporan_isi_lha_id')
            ->select(
                'ma.divisi as auditee',
                DB::raw('SUM(CASE WHEN plr.status_tindak_lanjut = "closed" THEN 1 ELSE 0 END) as closed_count'),
                DB::raw('SUM(CASE WHEN plr.status_tindak_lanjut = "on_progress" THEN 1 ELSE 0 END) as progress_count'),
                DB::raw('SUM(CASE WHEN plr.status_tindak_lanjut = "open" OR plr.status_tindak_lanjut IS NULL THEN 1 ELSE 0 END) as open_count')
            )
            ->whereIn('pa.id', $planIds)
            ->whereNotNull('ma.divisi')
            ->groupBy('ma.id', 'ma.divisi')
            ->orderByDesc('open_count')
            ->limit(8)
            ->get();
            
        $stackedCategories = [];
        $stackedClosed = [];
        $stackedProgress = [];
        $stackedOpen = [];
        
        foreach($temuanTl as $t) {
            $stackedCategories[] = strlen($t->auditee) > 15 ? substr($t->auditee, 0, 15).'...' : $t->auditee;
            $stackedClosed[] = (int)$t->closed_count;
            $stackedProgress[] = (int)$t->progress_count;
            $stackedOpen[] = (int)$t->open_count;
        }

        // 4. Data for Section 3: Temuan per Divisi
        $divisiTemuan = DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->select('ma.divisi', DB::raw('count(pt.id) as total'))
            ->whereIn('pa.id', $planIds)
            ->whereNotNull('ma.divisi')
            ->groupBy('ma.divisi')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
            
        $divisiCategories = [];
        $divisiData = [];
        foreach($divisiTemuan as $d) {
            $divisiCategories[] = strlen($d->divisi) > 15 ? substr($d->divisi, 0, 15).'...' : $d->divisi;
            $divisiData[] = (int)$d->total;
        }

        // 5. Data for Section 4: Top 10 Kode Risiko
        $topRisks = DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('master_kode_risk as mkr', 'pt.kode_risk_id', '=', 'mkr.id')
            ->select('mkr.kode_risiko', 'mkr.deskripsi_risiko', 'mkr.kelompok_risiko', DB::raw('count(pt.id) as total'))
            ->whereIn('pha.perencanaan_audit_id', $planIds)
            ->groupBy('mkr.id', 'mkr.kode_risiko', 'mkr.deskripsi_risiko', 'mkr.kelompok_risiko')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
            
        $riskCategories = [];
        $riskDescriptions = [];
        $riskData = [];
        foreach($topRisks as $r) {
            $riskCategories[] = $r->kode_risiko;
            $riskDescriptions[] = $r->deskripsi_risiko;
            $riskData[] = (int)$r->total;
        }
        
        // Heatmap Data (Divisi vs Risiko)
        $heatmapQuery = DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->join('master_kode_risk as mkr', 'pt.kode_risk_id', '=', 'mkr.id')
            ->select('ma.divisi', 'mkr.kode_risiko', 'mkr.deskripsi_risiko', DB::raw('count(pt.id) as total'))
            ->whereIn('pa.id', $planIds)
            ->whereNotNull('ma.divisi')
            ->groupBy('ma.divisi', 'mkr.kode_risiko', 'mkr.deskripsi_risiko')
            ->get();
            
        $heatmapDivisis = $divisiTemuan->pluck('divisi')->toArray();
        $heatmapRisks = $topRisks->pluck('kode_risiko')->toArray();
        $heatmapRisksDesc = $topRisks->pluck('deskripsi_risiko')->toArray();
        
        $heatmapData = [];
        
        foreach($heatmapDivisis as $yIndex => $divisi) {
            foreach($heatmapRisks as $xIndex => $riskKode) {
                $count = 0;
                $desc = $heatmapRisksDesc[$xIndex] ?? '';
                foreach($heatmapQuery as $hq) {
                    if($hq->divisi == $divisi && $hq->kode_risiko == $riskKode) {
                        $count = (int)$hq->total;
                        $desc = $hq->deskripsi_risiko;
                        break;
                    }
                }
                $heatmapData[] = [$xIndex, $yIndex, $count, $desc];
            }
        }
        
        // Clean up labels for heatmap
        $heatmapDivisiLabels = array_map(function($d) {
            return strlen($d) > 15 ? substr($d, 0, 15).'...' : $d;
        }, $heatmapDivisis);

        return view('audit.dashboard.analitik', compact(
            'masterDivisi',
            'masterUnit',
            'startDate',
            'endDate',
            'divisiId',
            'unitId',

            'totalDirencanakan',
            'totalTerealisasi',
            'totalTemuan',
            'rekomendasiOpen',
            'rekomendasiClosed',
            'rekomendasiOnProgress',
            'percentClosed',
            
            'trenBulan',
            'trenSelesai',
            
            'agingCategories',
            'agingData',
            'agingDetails',
            
            'statusCounts',
            
            'stackedCategories',
            'stackedClosed',
            'stackedProgress',
            'stackedOpen',
            
            'divisiCategories',
            'divisiData',
            
            'riskCategories',
            'riskDescriptions',
            'riskData',
            
            'heatmapDivisiLabels',
            'heatmapRisks',
            'heatmapData'
        ));
    }

    /**
     * Return full aging detail for a given bucket as JSON (for modal drill-down).
     * Query param: bucket = 'Sesuai Target' | '< 30 Hari' | '31-60 Hari' | '61-90 Hari' | '> 90 Hari'
     */
    public function agingDetail(Request $request)
    {
        $bucket = $request->get('bucket', '');

        $rows = DB::table('penutup_lha_rekomendasi as plr')
            ->join('pelaporan_temuan as pt', 'plr.pelaporan_isi_lha_id', '=', 'pt.id')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->leftJoin('master_unit as mu', 'pa.unit_id', '=', 'mu.id')
            ->whereIn('plr.status_tindak_lanjut', ['open', 'on_progress'])
            ->whereNotNull('plr.target_waktu')
            ->select(
                'plr.id',
                'plr.rekomendasi',
                'plr.target_waktu',
                'plr.status_tindak_lanjut',
                'ma.divisi',
                'mu.nama_unit as unit',
                'pa.nomor_surat_tugas'
            )
            ->get();

        $now = Carbon::now();
        $filtered = $rows->filter(function ($row) use ($bucket, $now) {
            $target = Carbon::parse($row->target_waktu);
            $diff = $target >= $now ? 0 : (int) $target->diffInDays($now);

            return match ($bucket) {
                'Sesuai Target' => $target >= $now,
                '< 30 Hari'    => $target < $now && $diff <= 30,
                '31-60 Hari'   => $diff > 30 && $diff <= 60,
                '61-90 Hari'   => $diff > 60 && $diff <= 90,
                '> 90 Hari'    => $diff > 90,
                default        => false,
            };
        })->map(function ($row) use ($now) {
            $target = Carbon::parse($row->target_waktu);
            $daysLate = $target >= $now ? 0 : (int) $target->diffInDays($now);
            return [
                'id'               => $row->id,
                'divisi'           => $row->divisi ?? '-',
                'unit'             => $row->unit ?? '-',
                'nomor_surat_tugas'=> $row->nomor_surat_tugas,
                'status'           => $row->status_tindak_lanjut,
                'target'           => $target->format('d M Y'),
                'days_late'        => $daysLate,
                'rekomendasi'      => $row->rekomendasi,
            ];
        })->sortByDesc('days_late')->values();

        return response()->json([
            'bucket' => $bucket,
            'total'  => $filtered->count(),
            'data'   => $filtered,
        ]);
    }
}
