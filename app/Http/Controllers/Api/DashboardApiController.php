<?php

namespace App\Http\Controllers\Api;

use App\Services\DashboardCacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterArea;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends BaseApiController
{
    /**
     * Dashboard Analitik — summary data audit.
     */
    public function analitik(Request $request): JsonResponse
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;
        $divisiId  = $request->divisi_id;
        $areaId    = $request->area_id;

        $masterDivisi = cache()->remember('dashboard_master_divisi', 3600, function () {
            return MasterAuditee::select('id', 'nama_bidang')->orderBy('nama_bidang')->get();
        });
        $masterArea = cache()->remember('dashboard_master_area', 3600, function () {
            return MasterArea::select('id', 'nama_area')->orderBy('nama_area')->get();
        });

        $hasFilters = $startDate || $endDate || $divisiId || $areaId;

        if (! $hasFilters) {
            $cache  = app(DashboardCacheService::class);
            $cached = $cache->get('dashboard_analitik');

            if ($cached) {
                return $this->success([
                    'filters'   => compact('masterDivisi', 'masterArea'),
                    'summary'   => [
                        'total_direncanakan'   => $cached['totalDirencanakan'],
                        'total_terealisasi'    => $cached['totalTerealisasi'],
                        'total_temuan'         => $cached['totalTemuan'],
                        'rekomendasi_open'     => $cached['rekomendasiStatus']['open'] ?? 0,
                        'rekomendasi_closed'   => $cached['rekomendasiClosed'],
                        'rekomendasi_on_progress' => $cached['rekomendasiStatus']['on_progress'] ?? 0,
                        'percent_closed'       => $cached['percentClosed'],
                    ],
                    'tren'      => ['bulan' => ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'], 'selesai' => $cached['trenSelesai']],
                    'aging'     => ['categories' => $cached['agingCategories'], 'data' => $cached['agingData']],
                    'status'    => $cached['statusCounts'],
                    'stacked'   => ['categories' => $cached['stackedCategories'], 'closed' => $cached['stackedClosed'], 'progress' => $cached['stackedProgress'], 'open' => $cached['stackedOpen']],
                    'divisi'    => ['categories' => $cached['divisiCategories'], 'data' => $cached['divisiData']],
                    'risk'      => ['categories' => $cached['riskCategories'], 'descriptions' => $cached['riskDescriptions'], 'data' => $cached['riskData']],
                    'heatmap'   => ['divisi_labels' => $cached['heatmapDivisiLabels'], 'risks' => $cached['heatmapRisks'], 'data' => $cached['heatmapData']],
                ]);
            }
        }

        // Build a reusable filter closure for perencanaan_audit — used as EXISTS subquery
        // to avoid PHP-intermediate planIds (no UUID list sent back to MySQL)
        $filterPA = function ($q) use ($startDate, $endDate, $divisiId, $areaId) {
            $q->select(DB::raw(1))->from('perencanaan_audit as pa_f')
              ->whereColumn('pa_f.id', 'pa.id');
            if ($startDate) $q->whereDate('pa_f.tanggal_audit_mulai', '>=', $startDate);
            if ($endDate)   $q->whereDate('pa_f.tanggal_audit_sampai', '<=', $endDate);
            if ($divisiId)  $q->where('pa_f.auditee_id', $divisiId);
            if ($areaId)    $q->where('pa_f.area_id', $areaId);
        };

        // When no filters match any PA, add shortcut sentinel
        $totalDirencanakan = DB::table('perencanaan_audit as pa')->whereExists($filterPA)->count();

        // Terealisasi — subquery path, no PHP planIds
        $totalFromEM = DB::table('entry_meeting as em')
            ->join('program_kerja_audit as pka', 'em.program_kerja_audit_id', '=', 'pka.id')
            ->join('perencanaan_audit as pa', 'pka.perencanaan_audit_id', '=', 'pa.id')
            ->whereExists($filterPA)
            ->distinct('pka.perencanaan_audit_id')
            ->count('pka.perencanaan_audit_id');

        $totalFromFallback = DB::table('realisasi_audits as ra')
            ->join('perencanaan_audit as pa', 'ra.perencanaan_audit_id', '=', 'pa.id')
            ->whereExists($filterPA)
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('entry_meeting as em')
                  ->join('program_kerja_audit as pka', 'em.program_kerja_audit_id', '=', 'pka.id')
                  ->whereColumn('pka.perencanaan_audit_id', 'ra.perencanaan_audit_id');
            })
            ->distinct('ra.perencanaan_audit_id')
            ->count('ra.perencanaan_audit_id');

        $totalTerealisasi = $totalFromEM + $totalFromFallback;

        // Total Temuan — direct JOIN + EXISTS filter
        $totalTemuan = DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->whereExists($filterPA)
            ->count('pt.id');

        // Rekomendasi — direct JOIN chain + EXISTS filter
        $rekomendasiStatus = DB::table('penutup_lha_rekomendasi as plr')
            ->join('pelaporan_temuan as pt', 'plr.pelaporan_isi_lha_id', '=', 'pt.id')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->whereExists($filterPA)
            ->select('plr.status_tindak_lanjut', DB::raw('count(*) as total'))
            ->groupBy('plr.status_tindak_lanjut')
            ->pluck('total', 'status_tindak_lanjut')
            ->toArray();

        $rekomendasiOpen       = $rekomendasiStatus['open'] ?? 0;
        $rekomendasiClosed     = $rekomendasiStatus['closed'] ?? 0;
        $rekomendasiOnProgress = $rekomendasiStatus['on_progress'] ?? 0;
        $totalTl               = array_sum($rekomendasiStatus);
        $percentClosed         = $totalTl > 0 ? round(($rekomendasiClosed / $totalTl) * 100, 1) : 0;

        // Status Realisasi — JOIN + EXISTS filter
        $statusCounts = DB::table('realisasi_audits as ra')
            ->join('perencanaan_audit as pa', 'ra.perencanaan_audit_id', '=', 'pa.id')
            ->whereExists($filterPA)
            ->select('ra.status', DB::raw('count(*) as count'))
            ->groupBy('ra.status')
            ->pluck('count', 'status')
            ->toArray();

        // Tren Penyelesaian — JOIN + EXISTS filter
        $trenRaw = DB::table('penutup_lha_rekomendasi as plr')
            ->join('pelaporan_temuan as pt', 'plr.pelaporan_isi_lha_id', '=', 'pt.id')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->whereExists($filterPA)
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

        // Aging — JOIN + EXISTS filter
        $agingRaw = DB::table('penutup_lha_rekomendasi as plr')
            ->join('pelaporan_temuan as pt', 'plr.pelaporan_isi_lha_id', '=', 'pt.id')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->whereExists($filterPA)
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

        // Stacked Bar (Temuan vs TL) — JOIN + EXISTS filter
        $temuanTl = DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->leftJoin('penutup_lha_rekomendasi as plr', 'pt.id', '=', 'plr.pelaporan_isi_lha_id')
            ->whereExists($filterPA)
            ->select(
                'ma.nama_bidang as auditee',
                DB::raw('SUM(CASE WHEN plr.status_tindak_lanjut = "closed" THEN 1 ELSE 0 END) as closed_count'),
                DB::raw('SUM(CASE WHEN plr.status_tindak_lanjut = "on_progress" THEN 1 ELSE 0 END) as progress_count'),
                DB::raw('SUM(CASE WHEN plr.status_tindak_lanjut = "open" OR plr.status_tindak_lanjut IS NULL THEN 1 ELSE 0 END) as open_count')
            )
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

        // Temuan per Divisi — JOIN + EXISTS filter
        $divisiTemuan = DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->whereExists($filterPA)
            ->select('ma.nama_bidang as divisi', DB::raw('count(pt.id) as total'))
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

        // Top Risiko — JOIN + EXISTS filter
        $topRisks = DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_kode_risk as mkr', 'pt.kode_risk_id', '=', 'mkr.id')
            ->whereExists($filterPA)
            ->select('mkr.kode_risiko', 'mkr.deskripsi_risiko', 'mkr.kelompok_risiko', DB::raw('count(pt.id) as total'))
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

        // Heatmap — JOIN + EXISTS filter
        $heatmapQuery = DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->join('master_kode_risk as mkr', 'pt.kode_risk_id', '=', 'mkr.id')
            ->whereExists($filterPA)
            ->select('ma.nama_bidang as divisi', 'mkr.kode_risiko', 'mkr.deskripsi_risiko', DB::raw('count(pt.id) as total'))
            ->whereNotNull('ma.nama_bidang')
            ->groupBy('ma.nama_bidang', 'mkr.kode_risiko', 'mkr.deskripsi_risiko')
            ->get();

        $heatmapLookup = [];
        foreach ($heatmapQuery as $hq) {
            $heatmapLookup[$hq->divisi . '|' . $hq->kode_risiko] = [
                'total' => (int) $hq->total,
                'desc'  => $hq->deskripsi_risiko,
            ];
        }

        $heatmapDivisis = [];
        foreach ($divisiTemuan as $d) {
            $heatmapDivisis[] = $d->divisi;
        }
        $heatmapRisks = [];
        foreach ($topRisks as $r) {
            $heatmapRisks[] = $r->kode_risiko;
        }

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

        return $this->success([
            'filters' => compact('masterDivisi', 'masterArea'),
            'summary' => [
                'total_direncanakan'      => $totalDirencanakan,
                'total_terealisasi'       => $totalTerealisasi,
                'total_temuan'            => $totalTemuan,
                'rekomendasi_open'        => $rekomendasiOpen,
                'rekomendasi_closed'      => $rekomendasiClosed,
                'rekomendasi_on_progress' => $rekomendasiOnProgress,
                'percent_closed'          => $percentClosed,
            ],
            'tren'    => ['bulan' => ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'], 'selesai' => $trenSelesai],
            'aging'   => ['categories' => $agingCategories, 'data' => $agingData],
            'status'  => $statusCounts,
            'stacked' => ['categories' => $stackedCategories, 'closed' => $stackedClosed, 'progress' => $stackedProgress, 'open' => $stackedOpen],
            'divisi'  => ['categories' => $divisiCategories, 'data' => $divisiData],
            'risk'    => ['categories' => $riskCategories, 'descriptions' => $riskDescriptions, 'data' => $riskData],
            'heatmap' => ['divisi_labels' => $heatmapDivisiLabels, 'risks' => $heatmapRisks, 'data' => $heatmapData],
        ]);
    }

    /**
     * Aging detail — data drill-down per bucket.
     */
    public function agingDetail(Request $request): JsonResponse
    {
        $bucket = $request->input('bucket', '');

        $bucketSql = match ($bucket) {
            'Sesuai Target' => 'plr.target_waktu >= NOW()',
            '< 30 Hari'     => 'plr.target_waktu < NOW() AND DATEDIFF(NOW(), plr.target_waktu) <= 30',
            '31-60 Hari'    => 'DATEDIFF(NOW(), plr.target_waktu) > 30 AND DATEDIFF(NOW(), plr.target_waktu) <= 60',
            '61-90 Hari'    => 'DATEDIFF(NOW(), plr.target_waktu) > 60 AND DATEDIFF(NOW(), plr.target_waktu) <= 90',
            '> 90 Hari'     => 'DATEDIFF(NOW(), plr.target_waktu) > 90',
            default         => '1=0',
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
                'plr.id', 'plr.rekomendasi', 'plr.target_waktu', 'plr.status_tindak_lanjut',
                'ma.nama_bidang as divisi', 'ma_area.nama_area as unit', 'pa.nomor_surat_tugas',
                DB::raw('GREATEST(0, DATEDIFF(NOW(), plr.target_waktu)) as days_late')
            )
            ->orderByDesc('days_late')
            ->get()
            ->map(fn($row) => [
                'id'                => $row->id,
                'divisi'            => $row->divisi ?? '-',
                'unit'              => $row->unit ?? '-',
                'nomor_surat_tugas' => $row->nomor_surat_tugas,
                'status'            => $row->status_tindak_lanjut,
                'target'            => Carbon::parse($row->target_waktu)->format('d M Y'),
                'days_late'         => (int) $row->days_late,
                'rekomendasi'       => $row->rekomendasi,
            ])
            ->values();

        return $this->success([
            'bucket' => $bucket,
            'total'  => $rows->count(),
            'data'   => $rows,
        ]);
    }

    /**
     * Dashboard PKPT — rekapitulasi jadwal PKPT.
     */
    public function pkpt(Request $request): JsonResponse
    {
        $data = DB::table('jadwal_pkpt_audits')
            ->leftJoin('master_auditee', 'jadwal_pkpt_audits.auditee_id', '=', 'master_auditee.id')
            ->select('jadwal_pkpt_audits.*', 'master_auditee.nama_bidang')
            ->get();

        return $this->success($data);
    }

    /**
     * Dashboard Rekapitulasi Aktivitas Audit.
     * OPTIMIZED: Results are cached for 60 seconds. Explicit column select
     * avoids transferring all pa.* columns (including JSON blobs) for every row.
     */
    public function rekapitulasi(Request $request): JsonResponse
    {
        $tahun = (int) $request->input('tahun', date('Y'));
        $cacheKey = "dashboard_rekapitulasi_{$tahun}";

        $data = cache()->remember($cacheKey, 60, function () use ($tahun) {
            return DB::table('perencanaan_audit as pa')
                ->leftJoin('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
                ->leftJoin('master_area as area', 'pa.area_id', '=', 'area.id')
                ->whereYear('pa.tanggal_audit_mulai', $tahun)
                ->select(
                    'pa.id', 'pa.nomor_surat_tugas', 'pa.tanggal_surat_tugas',
                    'pa.jenis_audit', 'pa.tanggal_audit_mulai', 'pa.tanggal_audit_sampai',
                    'pa.periode_audit', 'pa.area_id', 'pa.auditee_id',
                    'ma.nama_bidang', 'area.nama_area'
                )
                ->orderBy('pa.tanggal_audit_mulai')
                ->get();
        });

        return $this->success($data);
    }
}
