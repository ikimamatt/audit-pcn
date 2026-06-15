<?php

namespace Tests\Feature;

use Tests\AuditApiTestCase;

class DashboardApiControllerTest extends AuditApiTestCase
{
    /**
     * Test all dashboard endpoints.
     */
    public function test_dashboard_endpoints(): void
    {
        $headers = $this->auditorHeaders();

        // 1. Analitik
        $response = $this->getJson('/api/v1/audit/dashboard/analitik', $headers);
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // 2. Aging Detail
        $response = $this->getJson('/api/v1/audit/dashboard/aging-detail', $headers);
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // 3. PKPT
        $response = $this->getJson('/api/v1/audit/dashboard/pkpt', $headers);
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // 4. Rekapitulasi
        $response = $this->getJson('/api/v1/audit/dashboard/rekapitulasi', $headers);
        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
