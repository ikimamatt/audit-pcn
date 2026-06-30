<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardCacheService
{
    /**
     * Read a cached dashboard payload.
     * Returns null when the key doesn't exist (first run / before cron fires).
     */
    public function get(string $key): ?array
    {
        $row = DB::table('dashboard_cache')->where('cache_key', $key)->first();

        if (!$row) {
            return null;
        }

        return json_decode($row->cache_data, true);
    }

    /**
     * Write (insert-or-update) a dashboard cache entry.
     */
    public function put(string $key, array $data): void
    {
        DB::table('dashboard_cache')->updateOrInsert(
            ['cache_key' => $key],
            [
                'cache_data'   => json_encode($data, JSON_UNESCAPED_UNICODE),
                'refreshed_at' => now(),
            ]
        );
    }

    /**
     * Get the last refresh timestamp for a cache key.
     */
    public function lastRefreshedAt(string $key): ?Carbon
    {
        $row = DB::table('dashboard_cache')->where('cache_key', $key)->value('refreshed_at');
        return $row ? Carbon::parse($row) : null;
    }

    // ──────────────────────────────────────────────────────
    //  AGGREGATION QUERIES — called by the Cron command
    // ──────────────────────────────────────────────────────

    /**
     * Dashboard Analitik — KPI cards, charts, heatmap.
     *
     * OPTIMIZED: Replaces PHP-intermediate planIds (pluck all UUIDs into memory
     * then pass back as whereIn) with direct JOIN subqueries. This avoids sending
     * thousands of UUIDs over the wire and lets MySQL use indexes properly.
     */
    public function buildAnalitikData(): array
    {
        // KPI: Total Direncanakan
        $totalDirencanakan = DB::table('perencanaan_audit')->count();

        // KPI: Total Terealisasi — single query via subquery JOIN (no PHP planIds list)
        $totalFromEM = DB::table('entry_meeting as em')
            ->join('program_kerja_audit as pka', 'em.program_kerja_audit_id', '=', 'pka.id')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))->from('perencanaan_audit')->whereColumn('perencanaan_audit.id', 'pka.perencanaan_audit_id');
            })
            ->distinct('pka.perencanaan_audit_id')
            ->count('pka.perencanaan_audit_id');

        $totalFromFallback = DB::table('realisasi_audits as ra')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('entry_meeting as em')
                  ->join('program_kerja_audit as pka', 'em.program_kerja_audit_id', '=', 'pka.id')
                  ->whereColumn('pka.perencanaan_audit_id', 'ra.perencanaan_audit_id');
            })
            ->distinct('perencanaan_audit_id')
            ->count('perencanaan_audit_id');

        $totalTerealisasi = $totalFromEM + $totalFromFallback;

        // KPI: Total Temuan — direct JOIN, no whereIn
        $totalTemuan = DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->count('pt.id');

        // KPI: Rekomendasi status — direct JOIN chain
        $rekomendasiStatus = DB::table('penutup_lha_rekomendasi as plr')
            ->join('pelaporan_temuan as pt', 'plr.pelaporan_isi_lha_id', '=', 'pt.id')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->select('plr.status_tindak_lanjut', DB::raw('count(*) as total'))
            ->groupBy('plr.status_tindak_lanjut')
            ->pluck('total', 'status_tindak_lanjut')
            ->toArray();

        $rekomendasiClosed = $rekomendasiStatus['closed'] ?? 0;
        $totalTl = array_sum($rekomendasiStatus);
        $percentClosed = $totalTl > 0 ? round(($rekomendasiClosed / $totalTl) * 100, 1) : 0;

        // Tren Penyelesaian — aggregate by month directly in SQL
        $trenRaw = DB::table('penutup_lha_rekomendasi as plr')
            ->join('pelaporan_temuan as pt', 'plr.pelaporan_isi_lha_id', '=', 'pt.id')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
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

        // Aging — classify in SQL using CASE WHEN, direct JOIN chain
        $agingRaw = DB::table('penutup_lha_rekomendasi as plr')
            ->join('pelaporan_temuan as pt', 'plr.pelaporan_isi_lha_id', '=', 'pt.id')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
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

        // Status Realisasi — direct JOIN
        $statusCounts = DB::table('realisasi_audits as ra')
            ->join('perencanaan_audit as pa', 'ra.perencanaan_audit_id', '=', 'pa.id')
            ->select('ra.status', DB::raw('count(*) as count'))
            ->groupBy('ra.status')
            ->pluck('count', 'status')
            ->toArray();

        // Temuan vs TL (Stacked Bar) — top 8, direct JOIN
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

        // Temuan per Divisi — top 8, direct JOIN
        $divisiTemuan = DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
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

        // Top Risiko — top 8, direct JOIN
        $topRisks = DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_kode_risk as mkr', 'pt.kode_risk_id', '=', 'mkr.id')
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

        // Heatmap — pivot in SQL, direct JOIN
        $heatmapQuery = DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->join('master_kode_risk as mkr', 'pt.kode_risk_id', '=', 'mkr.id')
            ->select('ma.nama_bidang as divisi', 'mkr.kode_risiko', 'mkr.deskripsi_risiko', DB::raw('count(pt.id) as total'))
            ->whereNotNull('ma.nama_bidang')
            ->groupBy('ma.nama_bidang', 'mkr.kode_risiko', 'mkr.deskripsi_risiko')
            ->get();

        // Build heatmap using indexed lookup instead of triple nested loop
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

        return compact(
            'totalDirencanakan', 'totalTerealisasi', 'totalTemuan',
            'rekomendasiStatus', 'rekomendasiClosed', 'percentClosed',
            'trenSelesai', 'agingCategories', 'agingData',
            'statusCounts',
            'stackedCategories', 'stackedClosed', 'stackedProgress', 'stackedOpen',
            'divisiCategories', 'divisiData',
            'riskCategories', 'riskDescriptions', 'riskData',
            'heatmapDivisiLabels', 'heatmapRisks', 'heatmapData'
        );
    }

    /**
     * Dashboard Rencana PKPT — PKA summary with milestones.
     *
     * OPTIMIZED: Replaces 4 correlated subqueries per row (for jumlah_risiko,
     * jumlah_milestone, first/last milestone dates) with pre-aggregated LEFT JOINs.
     * For N PKA rows: was 4N+1 queries, now is 1 query.
     */
    public function buildRencanaPkptData(): array
    {
        $dashboardData = DB::table('program_kerja_audit as pka')
            ->join('perencanaan_audit as pa', 'pka.perencanaan_audit_id', '=', 'pa.id')
            ->leftJoin('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->leftJoin('master_jenis_audit as mja', 'pa.jenis_audit_id', '=', 'mja.id')
            ->leftJoin('entry_meeting as em', 'em.program_kerja_audit_id', '=', 'pka.id')
            // Pre-aggregate milestone stats — replaces 4 correlated subqueries per row
            ->leftJoin(
                DB::raw('(SELECT program_kerja_audit_id,
                    COUNT(*) as jumlah_milestone,
                    MIN(tanggal_mulai) as first_milestone_start,
                    MAX(tanggal_selesai) as last_milestone_end,
                    MAX(CASE WHEN nama_milestone = \'Entry Meeting\' THEN tanggal_mulai END) as entry_milestone_start,
                    MAX(CASE WHEN nama_milestone = \'Exit Meeting\' THEN tanggal_selesai END) as exit_milestone_end
                FROM pka_milestone GROUP BY program_kerja_audit_id) as ms_agg'),
                'ms_agg.program_kerja_audit_id', '=', 'pka.id'
            )
            // Pre-aggregate risiko count via proses_bisnis join
            ->leftJoin(
                DB::raw('(SELECT ppb.program_kerja_audit_id, COUNT(pr.id) as jumlah_risiko
                FROM pka_proses_bisnis ppb
                LEFT JOIN pka_risiko pr ON pr.pka_proses_bisnis_id = ppb.id
                GROUP BY ppb.program_kerja_audit_id) as risiko_agg'),
                'risiko_agg.program_kerja_audit_id', '=', 'pka.id'
            )
            ->select(
                'pka.id',
                'pka.no_pka',
                'pka.tanggal_pka',
                'pa.nomor_surat_tugas',
                'pa.auditor',
                'ma.nama_bidang',
                DB::raw('COALESCE(mja.nama_jenis_audit, pa.jenis_audit, "Audit Operasional") as jenis_audit'),
                'em.actual_meeting_date',
                DB::raw('COALESCE(risiko_agg.jumlah_risiko, 0) as jumlah_risiko'),
                DB::raw('COALESCE(ms_agg.jumlah_milestone, 0) as jumlah_milestone'),
                DB::raw('ms_agg.first_milestone_start'),
                DB::raw('ms_agg.last_milestone_end'),
                DB::raw('ms_agg.entry_milestone_start'),
                DB::raw('ms_agg.exit_milestone_end')
            )
            ->get()
            ->map(function ($row) {
                // Build auditee name
                $auditeeName = $row->nama_bidang ?? 'Unknown';

                // Auditor count
                $jumlahAuditor = 1;
                if ($row->auditor) {
                    $decoded = is_array($row->auditor) ? $row->auditor : json_decode($row->auditor, true);
                    $jumlahAuditor = is_array($decoded) ? count($decoded) : 1;
                }

                // Milestone dates
                $rencanaMulai = $row->entry_milestone_start ?? $row->first_milestone_start;
                $rencanaSelesai = $row->exit_milestone_end ?? $row->last_milestone_end;

                $rencanaMulaiFormatted = $rencanaMulai ? Carbon::parse($rencanaMulai)->format('d M Y') : '-';
                $rencanaSelesaiFormatted = $rencanaSelesai ? Carbon::parse($rencanaSelesai)->format('d M Y') : '-';

                // Realisasi & Status
                $realisasiMulai = '-';
                $status = 'Belum Dimulai';

                if ($row->actual_meeting_date) {
                    $realisasiMulai = Carbon::parse($row->actual_meeting_date)->format('d M Y');
                    $status = 'Sedang Berlangsung';
                }

                if ($rencanaMulai && $rencanaSelesai) {
                    $today = Carbon::now();
                    $start = Carbon::parse($rencanaMulai);
                    $end = Carbon::parse($rencanaSelesai);

                    if ($row->actual_meeting_date) {
                        $status = 'Sedang Berlangsung';
                    } elseif ($today->lt($start)) {
                        $status = 'Belum Dimulai';
                    } elseif ($today->between($start, $end)) {
                        $status = 'Sedang Berlangsung';
                    } elseif ($today->gt($end)) {
                        $status = 'Terlambat';
                    }
                }

                return [
                    'id'                => $row->id,
                    'no_pka'            => $row->no_pka,
                    'tanggal_pka'       => Carbon::parse($row->tanggal_pka)->format('d M Y'),
                    'tanggal_pka_raw'   => $row->tanggal_pka,
                    'surat_tugas'       => $row->nomor_surat_tugas ?? '-',
                    'auditee'           => $auditeeName,
                    'jenis_audit'       => $row->jenis_audit,
                    'jumlah_auditor'    => $jumlahAuditor,
                    'jumlah_risiko'     => (int) $row->jumlah_risiko,
                    'jumlah_milestone'  => (int) $row->jumlah_milestone,
                    'rencana_mulai'     => $rencanaMulaiFormatted,
                    'rencana_selesai'   => $rencanaSelesaiFormatted,
                    'realisasi_mulai'   => $realisasiMulai,
                    'realisasi_selesai' => '-',
                    'status'            => $status,
                ];
            })
            ->toArray();

        // Pie chart counts
        $collection = collect($dashboardData);
        $statusSelesai = $collection->where('status', 'Selesai')->count();
        $statusBerlangsung = $collection->where('status', 'Sedang Berlangsung')->count();
        $statusBelum = $collection->where('status', 'Belum Dimulai')->count();
        $statusTerlambat = $collection->where('status', 'Terlambat')->count();

        return compact('dashboardData', 'statusSelesai', 'statusBerlangsung', 'statusBelum', 'statusTerlambat');
    }

    /**
     * Rekapitulasi Aktivitas Audit — all counts via raw SQL.
     * Replaces 72 individual queries with 1 UNION + 1 aggregate per status.
     */
    public function buildRekapitulasiData(int $selectedYear): array
    {
        // PKA Status — single aggregate query
        $pkaStatusRaw = DB::select("
            SELECT
                CASE
                    WHEN em.actual_meeting_date IS NOT NULL AND (
                        ms_exit.tanggal_selesai IS NULL OR NOW() <= ms_exit.tanggal_selesai
                    ) THEN 'Sedang Berlangsung'
                    WHEN em.actual_meeting_date IS NOT NULL AND ms_exit.tanggal_selesai IS NOT NULL AND NOW() > ms_exit.tanggal_selesai THEN 'Selesai'
                    WHEN ms_entry.tanggal_mulai IS NOT NULL AND NOW() < ms_entry.tanggal_mulai THEN 'Belum Dimulai'
                    WHEN ms_entry.tanggal_mulai IS NOT NULL AND ms_exit.tanggal_selesai IS NOT NULL AND NOW() BETWEEN ms_entry.tanggal_mulai AND ms_exit.tanggal_selesai THEN 'Sedang Berlangsung'
                    WHEN ms_exit.tanggal_selesai IS NOT NULL AND NOW() > ms_exit.tanggal_selesai THEN 'Terlambat'
                    ELSE 'Belum Dimulai'
                END as status,
                COUNT(*) as total
            FROM program_kerja_audit pka
            LEFT JOIN entry_meeting em ON em.program_kerja_audit_id = pka.id
            LEFT JOIN (
                SELECT program_kerja_audit_id, MIN(tanggal_mulai) as tanggal_mulai
                FROM pka_milestone WHERE nama_milestone = 'Entry Meeting'
                GROUP BY program_kerja_audit_id
            ) ms_entry ON ms_entry.program_kerja_audit_id = pka.id
            LEFT JOIN (
                SELECT program_kerja_audit_id, MAX(tanggal_selesai) as tanggal_selesai
                FROM pka_milestone WHERE nama_milestone = 'Exit Meeting'
                GROUP BY program_kerja_audit_id
            ) ms_exit ON ms_exit.program_kerja_audit_id = pka.id
            GROUP BY status
        ");

        $pkaStatusData = ['Selesai' => 0, 'Sedang Berlangsung' => 0, 'Belum Dimulai' => 0, 'Terlambat' => 0];
        foreach ($pkaStatusRaw as $row) {
            if (isset($pkaStatusData[$row->status])) {
                $pkaStatusData[$row->status] = (int) $row->total;
            }
        }

        // Aktivitas counts — simple count per table
        $aktivitasData = [
            'Entry Meeting'          => DB::table('entry_meeting')->count(),
            'Walkthrough Audit'      => DB::table('walkthrough_audit')->count(),
            'TOD BPM Audit'          => DB::table('tod_bpm_audit')->count(),
            'TOE Audit'              => DB::table('toe_audit')->count(),
            'Exit Meeting'           => DB::table('exit_meeting_uploads')->count(),
            'Pelaporan Hasil Audit'  => DB::table('pelaporan_hasil_audit')->count(),
        ];

        // Bulanan — UNION ALL query (replaces 72 individual queries → 1 query)
        $bulananRaw = DB::select("
            SELECT model_type, MONTH(created_at) as bulan, COUNT(*) as total
            FROM (
                SELECT 'Entry Meeting' as model_type, created_at FROM entry_meeting WHERE YEAR(created_at) = ?
                UNION ALL
                SELECT 'Walkthrough', created_at FROM walkthrough_audit WHERE YEAR(created_at) = ?
                UNION ALL
                SELECT 'TOD BPM', created_at FROM tod_bpm_audit WHERE YEAR(created_at) = ?
                UNION ALL
                SELECT 'TOE', created_at FROM toe_audit WHERE YEAR(created_at) = ?
                UNION ALL
                SELECT 'Exit Meeting', created_at FROM exit_meeting_uploads WHERE YEAR(created_at) = ?
                UNION ALL
                SELECT 'Pelaporan', created_at FROM pelaporan_hasil_audit WHERE YEAR(created_at) = ?
            ) combined
            GROUP BY model_type, bulan
            ORDER BY bulan
        ", [$selectedYear, $selectedYear, $selectedYear, $selectedYear, $selectedYear, $selectedYear]);

        $months = [];
        $bulananData = [];
        $types = ['Entry Meeting', 'Walkthrough', 'TOD BPM', 'TOE', 'Exit Meeting', 'Pelaporan'];

        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create(null, $i, 1)->translatedFormat('M');
            $months[] = $monthName;
            $bulananData[$monthName] = array_fill_keys($types, 0);
        }

        foreach ($bulananRaw as $row) {
            $monthName = Carbon::create(null, (int) $row->bulan, 1)->translatedFormat('M');
            if (isset($bulananData[$monthName][$row->model_type])) {
                $bulananData[$monthName][$row->model_type] = (int) $row->total;
            }
        }

        // Approval status — 1 UNION query replaces 18 individual queries
        $approvalRaw = DB::select("
            SELECT status_val, SUM(cnt) as total FROM (
                SELECT status_approval as status_val, COUNT(*) as cnt FROM entry_meeting WHERE status_approval IN ('approved','pending','rejected') GROUP BY status_approval
                UNION ALL
                SELECT status_approval, COUNT(*) FROM walkthrough_audit WHERE status_approval IN ('approved','pending','rejected') GROUP BY status_approval
                UNION ALL
                SELECT status_approval, COUNT(*) FROM tod_bpm_audit WHERE status_approval IN ('approved','pending','rejected') GROUP BY status_approval
                UNION ALL
                SELECT status_approval, COUNT(*) FROM toe_audit WHERE status_approval IN ('approved','pending','rejected') GROUP BY status_approval
                UNION ALL
                SELECT status_approval, COUNT(*) FROM pelaporan_hasil_audit WHERE status_approval IN ('approved','pending','rejected') GROUP BY status_approval
                UNION ALL
                SELECT CASE WHEN approve = 1 THEN 'approved' ELSE 'pending' END, COUNT(*) FROM exit_meeting_uploads GROUP BY approve
            ) combined
            GROUP BY status_val
        ");

        $approvalData = ['Approved' => 0, 'Pending' => 0, 'Rejected' => 0];
        foreach ($approvalRaw as $row) {
            $key = ucfirst($row->status_val);
            if (isset($approvalData[$key])) {
                $approvalData[$key] += (int) $row->total;
            }
        }

        // Auditee Data (Top 10)
        $auditeeData = DB::table('perencanaan_audit as pa')
            ->join('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->select(
                'ma.nama_bidang',
                DB::raw('count(*) as total')
            )
            ->groupBy('pa.auditee_id', 'ma.nama_bidang')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return ['name' => $item->nama_bidang ?? 'Unknown', 'total' => $item->total];
            });

        // Total Summary — single UNION ALL query replaces 8 individual count() calls
        $totalSummaryRaw = DB::select("
            SELECT 'pka' as tipe, COUNT(*) as total FROM program_kerja_audit
            UNION ALL SELECT 'perencanaan', COUNT(*) FROM perencanaan_audit
            UNION ALL SELECT 'entry_meeting', COUNT(*) FROM entry_meeting
            UNION ALL SELECT 'walkthrough', COUNT(*) FROM walkthrough_audit
            UNION ALL SELECT 'tod', COUNT(*) FROM tod_bpm_audit
            UNION ALL SELECT 'toe', COUNT(*) FROM toe_audit
            UNION ALL SELECT 'exit', COUNT(*) FROM exit_meeting_uploads
            UNION ALL SELECT 'pelaporan', COUNT(*) FROM pelaporan_hasil_audit
        ");
        $summaryMap = collect($totalSummaryRaw)->pluck('total', 'tipe')->toArray();
        $totalSummary = [
            'total_pka'           => (int) ($summaryMap['pka'] ?? 0),
            'total_perencanaan'   => (int) ($summaryMap['perencanaan'] ?? 0),
            'total_entry_meeting' => (int) ($summaryMap['entry_meeting'] ?? 0),
            'total_walkthrough'   => (int) ($summaryMap['walkthrough'] ?? 0),
            'total_tod'           => (int) ($summaryMap['tod'] ?? 0),
            'total_toe'           => (int) ($summaryMap['toe'] ?? 0),
            'total_exit'          => (int) ($summaryMap['exit'] ?? 0),
            'total_pelaporan'     => (int) ($summaryMap['pelaporan'] ?? 0),
        ];

        return compact(
            'pkaStatusData', 'aktivitasData', 'bulananData', 'months',
            'approvalData', 'auditeeData', 'totalSummary'
        );
    }
}
