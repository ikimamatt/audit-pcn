<?php

namespace App\Console\Commands;

use App\Services\DashboardCacheService;
use Illuminate\Console\Command;

class RefreshDashboardCache extends Command
{
    protected $signature = 'dashboard:refresh-cache {--year= : Override year for rekapitulasi}';
    protected $description = 'Refresh all dashboard cache entries from the database';

    public function handle(DashboardCacheService $cache): int
    {
        $this->info('🔄 Refreshing dashboard cache...');
        $start = microtime(true);

        // 1. Dashboard Analitik
        $this->line('  → Dashboard Analitik...');
        $cache->put('dashboard_analitik', $cache->buildAnalitikData());
        $this->info('    ✅ Done');

        // 2. Dashboard Rencana PKPT
        $this->line('  → Dashboard Rencana PKPT...');
        $cache->put('dashboard_rencana_pkpt', $cache->buildRencanaPkptData());
        $this->info('    ✅ Done');

        // 3. Rekapitulasi Aktivitas Audit
        $year = $this->option('year') ?? date('Y');
        $this->line("  → Rekapitulasi Aktivitas (tahun {$year})...");
        $cache->put("rekapitulasi_{$year}", $cache->buildRekapitulasiData((int) $year));
        $this->info('    ✅ Done');

        $elapsed = round(microtime(true) - $start, 2);
        $this->newLine();
        $this->info("✅ All dashboard caches refreshed in {$elapsed}s");

        return self::SUCCESS;
    }
}
