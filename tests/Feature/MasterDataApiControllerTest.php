<?php

namespace Tests\Feature;

use Tests\AuditApiTestCase;

class MasterDataApiControllerTest extends AuditApiTestCase
{
    /**
     * Test all read-only master data endpoints.
     */
    public function test_master_data_endpoints(): void
    {
        $headers = $this->auditorHeaders();

        // 1. Auditee
        $response = $this->getJson('/api/v1/audit/master/auditee', $headers);
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // 2. Area
        $response = $this->getJson('/api/v1/audit/master/area', $headers);
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // 3. Jenis Audit
        $response = $this->getJson('/api/v1/audit/master/jenis-audit', $headers);
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // 4. Kode Risk
        $response = $this->getJson('/api/v1/audit/master/kode-risk', $headers);
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // 5. Kode AOI
        $response = $this->getJson('/api/v1/audit/master/kode-aoi', $headers);
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // 6. User
        $response = $this->getJson('/api/v1/audit/master/user', $headers);
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // 7. Region
        $response = $this->getJson('/api/v1/audit/master/region', $headers);
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // 8. Sub Bidang
        $response = $this->getJson('/api/v1/audit/master/sub-bidang', $headers);
        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
