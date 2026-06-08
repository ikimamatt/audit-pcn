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

        $masterDivisi = MasterAuditee::select('id', 'nama_bidang')->orderBy('nama_bidang')->get();
        $masterArea   = MasterArea::select('id', 'nama_area')->orderBy('nama_area')->get();

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

        // Compute with filters
        $queryPlan = DB::table('perencanaan_audit');
        if ($startDate) $queryPlan->whereDate('tanggal_audit_mulai', '>=', $startDate);
        if ($endDate)   $queryPlan->whereDate('tanggal_audit_sampai', '<=', $endDate);
        if ($divisiId)  $queryPlan->where('auditee_id', $divisiId);
        if ($areaId)    $queryPlan->where('area_id', $areaId);

        $totalDirencanakan = $queryPlan->count();
        $planIds = $queryPlan->pluck('id')->toArray();
        if (empty($planIds)) $planIds = [0];

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

        $rekomendasiOpen       = $rekomendasiStatus['open'] ?? 0;
        $rekomendasiClosed     = $rekomendasiStatus['closed'] ?? 0;
        $rekomendasiOnProgress = $rekomendasiStatus['on_progress'] ?? 0;
        $totalTl               = array_sum($rekomendasiStatus);
        $percentClosed         = $totalTl > 0 ? round(($rekomendasiClosed / $totalTl) * 100, 1) : 0;

        // Status Realisasi
        $statusCounts = DB::table('realisasi_audits')
            ->whereIn('perencanaan_audit_id', $planIds)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

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
            'status' => $statusCounts,
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
        $data = DB::table('jadwal_pkpt_audit')
            ->leftJoin('master_auditee', 'jadwal_pkpt_audit.auditee_id', '=', 'master_auditee.id')
            ->select('jadwal_pkpt_audit.*', 'master_auditee.nama_bidang')
            ->get();

        return $this->success($data);
    }

    /**
     * Dashboard Rekapitulasi Aktivitas Audit.
     */
    public function rekapitulasi(Request $request): JsonResponse
    {
        $tahun = $request->input('tahun', date('Y'));

        $data = DB::table('perencanaan_audit as pa')
            ->leftJoin('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->leftJoin('master_area as area', 'pa.area_id', '=', 'area.id')
            ->whereYear('pa.tanggal_audit_mulai', $tahun)
            ->select('pa.*', 'ma.nama_bidang', 'area.nama_area')
            ->orderBy('pa.tanggal_audit_mulai')
            ->get();

        return $this->success($data);
    }
}
