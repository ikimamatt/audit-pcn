<?php

namespace Tests\Feature;

use Tests\AuditApiTestCase;

class DashboardAuditCheckTest extends AuditApiTestCase
{
    /**
     * Test that the dashboard:audit-check console command executes successfully.
     */
    public function test_dashboard_audit_check_command(): void
    {
        $this->artisan('dashboard:audit-check')
            ->expectsOutputToContain('DEEP ANALYSIS: DASHBOARD vs DATABASE')
            ->expectsOutputToContain('KPI 1: Rencana Audit')
            ->expectsOutputToContain('KPI 2: Terealisasi')
            ->expectsOutputToContain('KPI 3: Total Temuan')
            ->expectsOutputToContain('KPI 4: Penyelesaian TL')
            ->expectsOutputToContain('ANALISIS SELESAI')
            ->assertExitCode(0);
    }
}
