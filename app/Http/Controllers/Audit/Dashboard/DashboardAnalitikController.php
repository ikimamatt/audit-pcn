<?php

namespace App\Http\Controllers\Audit\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DashboardCacheService;
use Illuminate\Http\Request;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterArea;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardAnalitikController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $divisiId = $request->divisi_id;
        $areaId = $request->area_id;

        $masterDivisi = MasterAuditee::select('id', 'nama_bidang')->orderBy('nama_bidang')->get();
        $masterArea = MasterArea::select('id', 'nama_area')->orderBy('nama_area')->get();

        // If filters are applied, compute on-the-fly (filtered); otherwise use cache
        $hasFilters = $startDate || $endDate || $divisiId || $areaId;

        if (!$hasFilters) {
            $cache = app(DashboardCacheService::class);
            $cached = $cache->get('dashboard_analitik');

            if ($cached) {
                // Unpack cached data
                $totalDirencanakan = $cached['totalDirencanakan'];
                $totalTerealisasi = $cached['totalTerealisasi'];
                $totalTemuan = $cached['totalTemuan'];
                $rekomendasiOpen = $cached['rekomendasiStatus']['open'] ?? 0;
                $rekomendasiClosed = $cached['rekomendasiClosed'];
                $rekomendasiOnProgress = $cached['rekomendasiStatus']['on_progress'] ?? 0;
                $percentClosed = $cached['percentClosed'];

                $trenBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                $trenSelesai = $cached['trenSelesai'];

                $agingCategories = $cached['agingCategories'];
                $agingData = $cached['agingData'];
                $agingDetails = $this->buildAgingDetails();

                $statusCounts = $cached['statusCounts'];

                $stackedCategories = $cached['stackedCategories'];
                $stackedClosed = $cached['stackedClosed'];
                $stackedProgress = $cached['stackedProgress'];
                $stackedOpen = $cached['stackedOpen'];

                $divisiCategories = $cached['divisiCategories'];
                $divisiData = $cached['divisiData'];

                $riskCategories = $cached['riskCategories'];
                $riskDescriptions = $cached['riskDescriptions'];
                $riskData = $cached['riskData'];

                $heatmapDivisiLabels = $cached['heatmapDivisiLabels'];
                $heatmapRisks = $cached['heatmapRisks'];
                $heatmapData = $cached['heatmapData'];

                return view('audit.dashboard.analitik', compact(
                    'masterDivisi', 'masterArea', 'startDate', 'endDate', 'divisiId', 'areaId',
                    'totalDirencanakan', 'totalTerealisasi', 'totalTemuan',
                    'rekomendasiOpen', 'rekomendasiClosed', 'rekomendasiOnProgress', 'percentClosed',
                    'trenBulan', 'trenSelesai',
                    'agingCategories', 'agingData', 'agingDetails',
                    'statusCounts',
                    'stackedCategories', 'stackedClosed', 'stackedProgress', 'stackedOpen',
                    'divisiCategories', 'divisiData',
                    'riskCategories', 'riskDescriptions', 'riskData',
                    'heatmapDivisiLabels', 'heatmapRisks', 'heatmapData'
                ));
            }
        }

        // Filtered path or cache miss — compute with filters applied via raw SQL
        $queryPlan = DB::table('perencanaan_audit');
        if ($startDate) $queryPlan->whereDate('tanggal_audit_mulai', '>=', $startDate);
        if ($endDate) $queryPlan->whereDate('tanggal_audit_sampai', '<=', $endDate);
        if ($divisiId) $queryPlan->where('auditee_id', $divisiId);
        if ($areaId) $queryPlan->where('area_id', $areaId);

        $totalDirencanakan = $queryPlan->count();
        $planIds = $queryPlan->pluck('id')->toArray();

        if (empty($planIds)) {
            $planIds = [0]; // prevent empty IN clause
        }

        // Terealisasi
        $emPlanIds = DB::table('entry_meeting')
            ->join('program_kerja_audit as pka', 'entry_meeting.program_kerja_audit_id', '=', 'pka.id')
            ->whereIn('pka.perencanaan_audit_id', $planIds)
            ->distinct()
            ->pluck('pka.perencanaan_audit_id');

        $totalFromEM = $emPlanIds->count();
        $totalFromFallback = DB::table('realisasi_audits')
            ->whereIn('perencanaan_audit_id', $planIds)
            ->when($emPlanIds->isNotEmpty(), fn($q) => $q->whereNotIn('perencanaan_audit_id', $emPlanIds))
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
            ->pluck('total', 'status_tindak_lanjut')
            ->toArray();

        $rekomendasiOpen = $rekomendasiStatus['open'] ?? 0;
        $rekomendasiClosed = $rekomendasiStatus['closed'] ?? 0;
        $rekomendasiOnProgress = $rekomendasiStatus['on_progress'] ?? 0;
        $totalTl = array_sum($rekomendasiStatus);
        $percentClosed = $totalTl > 0 ? round(($rekomendasiClosed / $totalTl) * 100, 1) : 0;

        // Tren — aggregate by month in SQL (no PHP loop)
        $trenBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $trenRaw = DB::table('penutup_lha_rekomendasi as plr')
            ->join('pelaporan_temuan as pt', 'plr.pelaporan_isi_lha_id', '=', 'pt.id')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->whereIn('pha.perencanaan_audit_id', $planIds)
            ->where('plr.status_tindak_lanjut', 'closed')
            ->whereNotNull('plr.real_waktu')
            ->select(DB::raw('MONTH(plr.real_waktu) as bulan'), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('MONTH(plr.real_waktu)'))
            ->pluck('total', 'bulan')
            ->toArray();

        $trenSelesai = [];
        for ($i = 1; $i <= 12; $i++) {
            $trenSelesai[] = $trenRaw[$i] ?? 0;
        }

        // Aging — CASE WHEN in SQL (replaces PHP foreach loop)
        $agingRaw = DB::table('penutup_lha_rekomendasi as plr')
            ->join('pelaporan_temuan as pt', 'plr.pelaporan_isi_lha_id', '=', 'pt.id')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->whereIn('pha.perencanaan_audit_id', $planIds)
            ->where(function ($q) {
                $q->whereIn('plr.status_tindak_lanjut', ['open', 'on_progress'])
                  ->orWhereNull('plr.status_tindak_lanjut');
            })
            ->whereNotNull('plr.target_waktu')
            ->select(DB::raw("
                CASE
                    WHEN plr.target_waktu >= NOW() THEN 'Sesuai Target'
                    WHEN DATEDIFF(NOW(), plr.target_waktu) <= 30 THEN '< 30 Hari'
                    WHEN DATEDIFF(NOW(), plr.target_waktu) <= 60 THEN '31-60 Hari'
                    WHEN DATEDIFF(NOW(), plr.target_waktu) <= 90 THEN '61-90 Hari'
                    ELSE '> 90 Hari'
                END as bucket
            "), DB::raw('COUNT(*) as total'))
            ->groupBy('bucket')
            ->pluck('total', 'bucket')
            ->toArray();

        $agingCategories = ['Sesuai Target', '< 30 Hari', '31-60 Hari', '61-90 Hari', '> 90 Hari'];
        $agingData = [];
        foreach ($agingCategories as $cat) {
            $agingData[] = $agingRaw[$cat] ?? 0;
        }

        $agingDetails = $this->buildAgingDetails($planIds);

        // Status Realisasi
        $statusCounts = DB::table('realisasi_audits')
            ->whereIn('perencanaan_audit_id', $planIds)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Stacked Bar, Divisi, Risk, Heatmap — reuse existing efficient queries
        $temuanTl = DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->leftJoin('penutup_lha_rekomendasi as plr', 'pt.id', '=', 'plr.pelaporan_isi_lha_id')
            ->select(
                'ma.nama_bidang as auditee',
                DB::raw('SUM(CASE WHEN plr.status_tindak_lanjut = "closed" THEN 1 ELSE 0 END) as closed_count'),
                DB::raw('SUM(CASE WHEN plr.status_tindak_lanjut = "on_progress" THEN 1 ELSE 0 END) as progress_count'),
                DB::raw('SUM(CASE WHEN plr.status_tindak_lanjut = "open" OR plr.status_tindak_lanjut IS NULL THEN 1 ELSE 0 END) as open_count')
            )
            ->whereIn('pa.id', $planIds)
            ->whereNotNull('ma.nama_bidang')
            ->groupBy('ma.id', 'ma.nama_bidang')
            ->orderByDesc('open_count')
            ->limit(8)
            ->get();

        $stackedCategories = [];
        $stackedClosed = [];
        $stackedProgress = [];
        $stackedOpen = [];
        foreach ($temuanTl as $t) {
            $stackedCategories[] = mb_strlen($t->auditee) > 15 ? mb_substr($t->auditee, 0, 15) . '...' : $t->auditee;
            $stackedClosed[] = (int) $t->closed_count;
            $stackedProgress[] = (int) $t->progress_count;
            $stackedOpen[] = (int) $t->open_count;
        }

        $divisiTemuan = DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->select('ma.nama_bidang as divisi', DB::raw('count(pt.id) as total'))
            ->whereIn('pa.id', $planIds)
            ->whereNotNull('ma.nama_bidang')
            ->groupBy('ma.nama_bidang')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $divisiCategories = [];
        $divisiData = [];
        foreach ($divisiTemuan as $d) {
            $divisiCategories[] = mb_strlen($d->divisi) > 15 ? mb_substr($d->divisi, 0, 15) . '...' : $d->divisi;
            $divisiData[] = (int) $d->total;
        }

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
        foreach ($topRisks as $r) {
            $riskCategories[] = $r->kode_risiko;
            $riskDescriptions[] = $r->deskripsi_risiko;
            $riskData[] = (int) $r->total;
        }

        // Heatmap — indexed lookup instead of triple nested foreach
        $heatmapQuery = DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->join('master_kode_risk as mkr', 'pt.kode_risk_id', '=', 'mkr.id')
            ->select('ma.nama_bidang as divisi', 'mkr.kode_risiko', 'mkr.deskripsi_risiko', DB::raw('count(pt.id) as total'))
            ->whereIn('pa.id', $planIds)
            ->whereNotNull('ma.nama_bidang')
            ->groupBy('ma.nama_bidang', 'mkr.kode_risiko', 'mkr.deskripsi_risiko')
            ->get();

        // Build indexed lookup for O(1) access instead of O(n) inner loop
        $heatmapLookup = [];
        foreach ($heatmapQuery as $hq) {
            $heatmapLookup[$hq->divisi . '|' . $hq->kode_risiko] = [
                'total' => (int) $hq->total,
                'desc'  => $hq->deskripsi_risiko,
            ];
        }

        $heatmapDivisis = $divisiTemuan->pluck('divisi')->toArray();
        $heatmapRisks = $topRisks->pluck('kode_risiko')->toArray();
        $heatmapData = [];

        foreach ($heatmapDivisis as $yIndex => $divisi) {
            foreach ($heatmapRisks as $xIndex => $riskKode) {
                $key = $divisi . '|' . $riskKode;
                $entry = $heatmapLookup[$key] ?? ['total' => 0, 'desc' => ''];
                $heatmapData[] = [$xIndex, $yIndex, $entry['total'], $entry['desc']];
            }
        }

        $heatmapDivisiLabels = array_map(function ($d) {
            return mb_strlen($d) > 15 ? mb_substr($d, 0, 15) . '...' : $d;
        }, $heatmapDivisis);

        return view('audit.dashboard.analitik', compact(
            'masterDivisi', 'masterArea', 'startDate', 'endDate', 'divisiId', 'areaId',
            'totalDirencanakan', 'totalTerealisasi', 'totalTemuan',
            'rekomendasiOpen', 'rekomendasiClosed', 'rekomendasiOnProgress', 'percentClosed',
            'trenBulan', 'trenSelesai',
            'agingCategories', 'agingData', 'agingDetails',
            'statusCounts',
            'stackedCategories', 'stackedClosed', 'stackedProgress', 'stackedOpen',
            'divisiCategories', 'divisiData',
            'riskCategories', 'riskDescriptions', 'riskData',
            'heatmapDivisiLabels', 'heatmapRisks', 'heatmapData'
        ));
    }

    /**
     * Build aging detail data (top 5 per bucket) for tooltip.
     * Uses SQL to do the heavy lifting.
     */
    private function buildAgingDetails(?array $planIds = null): array
    {
        $query = DB::table('penutup_lha_rekomendasi as plr')
            ->join('pelaporan_temuan as pt', 'plr.pelaporan_isi_lha_id', '=', 'pt.id')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->leftJoin('master_area as ma_area', 'pa.area_id', '=', 'ma_area.id')
            ->where(function ($q) {
                $q->whereIn('plr.status_tindak_lanjut', ['open', 'on_progress'])
                  ->orWhereNull('plr.status_tindak_lanjut');
            })
            ->whereNotNull('plr.target_waktu')
            ->select(
                'ma.nama_bidang as divisi',
                'ma_area.nama_area as unit',
                'plr.target_waktu',
                DB::raw("
                    CASE
                        WHEN plr.target_waktu >= NOW() THEN 'Sesuai Target'
                        WHEN DATEDIFF(NOW(), plr.target_waktu) <= 30 THEN '< 30 Hari'
                        WHEN DATEDIFF(NOW(), plr.target_waktu) <= 60 THEN '31-60 Hari'
                        WHEN DATEDIFF(NOW(), plr.target_waktu) <= 90 THEN '61-90 Hari'
                        ELSE '> 90 Hari'
                    END as bucket
                "),
                DB::raw("GREATEST(0, DATEDIFF(NOW(), plr.target_waktu)) as days_late")
            );

        if ($planIds) {
            $query->whereIn('pha.perencanaan_audit_id', $planIds);
        }

        $rows = $query->orderByDesc(DB::raw("DATEDIFF(NOW(), plr.target_waktu)"))->get();

        $agingDetails = [
            'Sesuai Target' => [],
            '< 30 Hari'     => [],
            '31-60 Hari'    => [],
            '61-90 Hari'    => [],
            '> 90 Hari'     => [],
        ];

        foreach ($rows as $row) {
            if (isset($agingDetails[$row->bucket]) && count($agingDetails[$row->bucket]) < 5) {
                $agingDetails[$row->bucket][] = [
                    'divisi'    => $row->divisi ?? '-',
                    'unit'      => $row->unit ?? '-',
                    'days_late' => (int) $row->days_late,
                ];
            }
        }

        return $agingDetails;
    }

    /**
     * Return full aging detail for a given bucket as JSON (for modal drill-down).
     * Uses SQL CASE WHEN for bucket classification instead of PHP foreach.
     */
    public function agingDetail(Request $request)
    {
        $bucket = $request->input('bucket', '');

        $bucketSql = match ($bucket) {
            'Sesuai Target' => 'plr.target_waktu >= NOW()',
            '< 30 Hari'     => 'plr.target_waktu < NOW() AND DATEDIFF(NOW(), plr.target_waktu) <= 30',
            '31-60 Hari'    => 'DATEDIFF(NOW(), plr.target_waktu) > 30 AND DATEDIFF(NOW(), plr.target_waktu) <= 60',
            '61-90 Hari'    => 'DATEDIFF(NOW(), plr.target_waktu) > 60 AND DATEDIFF(NOW(), plr.target_waktu) <= 90',
            '> 90 Hari'     => 'DATEDIFF(NOW(), plr.target_waktu) > 90',
            default         => '1=0', // no match
        };

        $rows = DB::table('penutup_lha_rekomendasi as plr')
            ->join('pelaporan_temuan as pt', 'plr.pelaporan_isi_lha_id', '=', 'pt.id')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->leftJoin('master_area as ma_area', 'pa.area_id', '=', 'ma_area.id')
            ->whereIn('plr.status_tindak_lanjut', ['open', 'on_progress'])
            ->whereNotNull('plr.target_waktu')
            ->whereRaw($bucketSql)
            ->select(
                'plr.id',
                'plr.rekomendasi',
                'plr.target_waktu',
                'plr.status_tindak_lanjut',
                'ma.nama_bidang as divisi',
                'ma_area.nama_area as unit',
                'pa.nomor_surat_tugas',
                DB::raw('GREATEST(0, DATEDIFF(NOW(), plr.target_waktu)) as days_late')
            )
            ->orderByDesc('days_late')
            ->get()
            ->map(function ($row) {
                return [
                    'id'                => $row->id,
                    'divisi'            => $row->divisi ?? '-',
                    'unit'              => $row->unit ?? '-',
                    'nomor_surat_tugas' => $row->nomor_surat_tugas,
                    'status'            => $row->status_tindak_lanjut,
                    'target'            => Carbon::parse($row->target_waktu)->format('d M Y'),
                    'days_late'         => (int) $row->days_late,
                    'rekomendasi'       => $row->rekomendasi,
                ];
            })
            ->values();

        return response()->json([
            'bucket' => $bucket,
            'total'  => $rows->count(),
            'data'   => $rows,
        ]);
    }
}
