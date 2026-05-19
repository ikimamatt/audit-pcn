<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardAuditCheck extends Command
{
    protected $signature = 'dashboard:audit-check';
    protected $description = 'Deep verify dashboard metrics against the real database';

    public function handle()
    {
        $this->line("\n========================================");
        $this->line("  DEEP ANALYSIS: DASHBOARD vs DATABASE");
        $this->line("========================================\n");

        // =====================================================
        // KPI 1: Rencana Audit
        // =====================================================
        $totalRencana = DB::table('perencanaan_audit')->count();
        $this->info("=== KPI 1: Rencana Audit ===");
        $this->line("  DB Total      : {$totalRencana}");
        $this->line("  Dashboard Shows: 38");
        $this->line("  " . ($totalRencana == 38 ? "✅ SESUAI" : "❌ BEDA => perbedaan: ".abs($totalRencana-38)));

        // =====================================================
        // KPI 2: Terealisasi
        // =====================================================
        $this->line("\n=== KPI 2: Terealisasi (Entry Meeting + Fallback) ===");
        $emIds = DB::table('entry_meeting')
            ->join('program_kerja_audit as pka', 'entry_meeting.program_kerja_audit_id', '=', 'pka.id')
            ->distinct()
            ->pluck('pka.perencanaan_audit_id');
        $fromEM = $emIds->count();
        $fromFallback = DB::table('realisasi_audits')
            ->whereNotIn('perencanaan_audit_id', $emIds)
            ->distinct('perencanaan_audit_id')
            ->count('perencanaan_audit_id');
        $totalTerealisasi = $fromEM + $fromFallback;
        $this->line("  Dari Entry Meeting     : {$fromEM}");
        $this->line("  Dari Realisasi Fallback: {$fromFallback}");
        $this->line("  Total DB               : {$totalTerealisasi}");
        $this->line("  Dashboard Shows        : 26");
        $this->line("  " . ($totalTerealisasi == 26 ? "✅ SESUAI" : "❌ BEDA => perbedaan: ".abs($totalTerealisasi-26)));

        // =====================================================
        // KPI 3: Total Temuan
        // =====================================================
        $this->line("\n=== KPI 3: Total Temuan ===");
        $totalTemuan = DB::table('pelaporan_temuan')->count();
        $this->line("  DB Total      : {$totalTemuan}");
        $this->line("  Dashboard Shows: 63");
        $this->line("  " . ($totalTemuan == 63 ? "✅ SESUAI" : "❌ BEDA => perbedaan: ".abs($totalTemuan-63)));

        // =====================================================
        // KPI 4: % Penyelesaian TL
        // =====================================================
        $this->line("\n=== KPI 4: Penyelesaian TL ===");
        $tlCounts = DB::table('penutup_lha_rekomendasi')
            ->select('status_tindak_lanjut', DB::raw('count(*) as total'))
            ->groupBy('status_tindak_lanjut')
            ->pluck('total', 'status_tindak_lanjut')
            ->toArray();
        $closed    = $tlCounts['closed']      ?? 0;
        $onProgress= $tlCounts['on_progress'] ?? 0;
        $open      = $tlCounts['open']        ?? 0;
        $totalTl   = array_sum($tlCounts);
        $pct       = $totalTl > 0 ? round(($closed / $totalTl) * 100, 1) : 0;
        $this->line("  DB: Closed={$closed} | On_Progress={$onProgress} | Open={$open} | Total={$totalTl}");
        $this->line("  Pct: {$pct}%   Dashboard Shows: 31.6% (Closed:18, OnProgress:22, Open:17)");
        $this->line("  " . (abs($pct - 31.6) < 0.5 ? "✅ SESUAI" : "⚠️  BEDA NILAI"));

        // =====================================================
        // Status Pelaksanaan (Donut)
        // =====================================================
        $this->line("\n=== Status Pelaksanaan (Donut) ===");
        $statusDist = DB::table('realisasi_audits')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        foreach ($statusDist as $s => $c) {
            $this->line("  {$s}: {$c}");
        }
        $this->line("  Dashboard Shows: Selesai:13, On Progress:11, Belum:14");

        // =====================================================
        // Tren Penyelesaian Rekomendasi Audit (Line Chart)
        // =====================================================
        $this->line("\n=== Tren Penyelesaian Rekomendasi per Bulan ===");
        $tren = DB::table('penutup_lha_rekomendasi')
            ->where('status_tindak_lanjut', 'closed')
            ->whereNotNull('real_waktu')
            ->select(DB::raw('MONTH(real_waktu) as bln'), DB::raw('count(*) as total'))
            ->groupBy(DB::raw('MONTH(real_waktu)'))
            ->orderBy('bln')
            ->pluck('total', 'bln')
            ->toArray();
        $months = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Ags',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
        foreach ($months as $m => $lbl) {
            $v = $tren[$m] ?? 0;
            $bar = str_repeat('█', $v);
            $this->line("  {$lbl}: {$v} {$bar}");
        }

        // =====================================================
        // Aging Keterlambatan TL
        // =====================================================
        $this->line("\n=== Aging Keterlambatan TL ===");
        $aging = ['Sesuai Target' => 0, '< 30 Hari' => 0, '31-60 Hari' => 0, '61-90 Hari' => 0, '> 90 Hari' => 0];
        $openTLRows = DB::table('penutup_lha_rekomendasi')
            ->whereIn('status_tindak_lanjut', ['open', 'on_progress'])
            ->whereNotNull('target_waktu')
            ->get();
        $now = Carbon::now();
        foreach ($openTLRows as $tl) {
            $target = Carbon::parse($tl->target_waktu);
            if ($target >= $now)    { $aging['Sesuai Target']++; }
            elseif ($target->diffInDays($now) <= 30) { $aging['< 30 Hari']++; }
            elseif ($target->diffInDays($now) <= 60) { $aging['31-60 Hari']++; }
            elseif ($target->diffInDays($now) <= 90) { $aging['61-90 Hari']++; }
            else                                     { $aging['> 90 Hari']++; }
        }
        foreach ($aging as $k => $v) {
            $this->line("  {$k}: {$v}");
        }
        $this->line("  Dashboard Shows: Sesuai Target:~13, <30:~5, 31-60:~9, 61-90:~7, >90:~3");

        // =====================================================
        // Sebaran Temuan per Divisi
        // =====================================================
        $this->line("\n=== Sebaran Temuan per Divisi (Top 8) ===");
        DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('perencanaan_audit as pa', 'pha.perencanaan_audit_id', '=', 'pa.id')
            ->join('master_auditee as ma', 'pa.auditee_id', '=', 'ma.id')
            ->select('ma.divisi', DB::raw('count(pt.id) as total'))
            ->whereNotNull('ma.divisi')
            ->groupBy('ma.id', 'ma.divisi')
            ->orderByDesc('total')
            ->limit(8)
            ->get()
            ->each(function ($r) {
                $this->line("  {$r->divisi}: {$r->total}");
            });

        // =====================================================
        // Top Risiko
        // =====================================================
        $this->line("\n=== Top Klasifikasi Risiko (Top 8) ===");
        DB::table('pelaporan_temuan as pt')
            ->join('pelaporan_hasil_audit as pha', 'pt.pelaporan_hasil_audit_id', '=', 'pha.id')
            ->join('master_kode_risk as mkr', 'pt.kode_risk_id', '=', 'mkr.id')
            ->select('mkr.kode_risiko', DB::raw('count(pt.id) as total'))
            ->groupBy('mkr.id', 'mkr.kode_risiko')
            ->orderByDesc('total')
            ->limit(8)
            ->get()
            ->each(function ($r) {
                $this->line("  {$r->kode_risiko}: {$r->total}");
            });

        $this->line("\n========================================");
        $this->line("  ANALISIS SELESAI");
        $this->line("========================================\n");
    }
}
