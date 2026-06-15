<?php

namespace Tests\Feature;

use Tests\AuditApiTestCase;
use App\Models\MasterData\MasterAuditee;

class PkptApiControllerTest extends AuditApiTestCase
{
    /**
     * Test PKPT resource CRUD.
     */
    public function test_pkpt_crud_flow(): void
    {
        $headers = $this->auditorHeaders();
        $auditee = MasterAuditee::first();

        $payload = [
            'auditee_id' => $auditee->id,
            'jenis_audit' => 'Audit Operasional',
            'jumlah_auditor' => 3,
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-07-15',
        ];

        // 1. Create (Store)
        $responseStore = $this->postJson('/api/v1/audit/pkpt', $payload, $headers);
        $responseStore->assertStatus(201)
            ->assertJsonPath('success', true);

        $createdId = $responseStore->json('data.id');

        // 2. Read (Show)
        $this->getJson("/api/v1/audit/pkpt/{$createdId}", $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.jenis_audit', 'Audit Operasional');

        // 3. Update
        $payloadUpdate = $payload;
        $payloadUpdate['jenis_audit'] = 'Audit Khusus';

        $this->putJson("/api/v1/audit/pkpt/{$createdId}", $payloadUpdate, $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.jenis_audit', 'Audit Khusus');

        // 4. Index List
        $this->getJson('/api/v1/audit/pkpt', $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // 5. Delete (Destroy)
        $this->deleteJson("/api/v1/audit/pkpt/{$createdId}", [], $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // Verify it was deleted
        $this->getJson("/api/v1/audit/pkpt/{$createdId}", $headers)
            ->assertStatus(404);
    }
}
