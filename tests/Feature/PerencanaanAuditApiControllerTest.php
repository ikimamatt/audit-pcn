<?php

namespace Tests\Feature;

use Tests\AuditApiTestCase;
use App\Models\Audit\PerencanaanAudit;
use App\Models\MasterData\MasterUser;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterJenisAudit;
use App\Models\MasterData\MasterArea;

class PerencanaanAuditApiControllerTest extends AuditApiTestCase
{
    /**
     * Test planning CRUD flow.
     */
    public function test_perencanaan_audit_crud_flow(): void
    {
        $headers = $this->auditorHeaders();

        // 1. Form Data
        $this->getJson('/api/v1/audit/perencanaan/form-data', $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // 2. Nomor Surat Tugas Generator
        $this->getJson('/api/v1/audit/perencanaan/nomor-surat-tugas?jenis_audit=Audit+Operasional', $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['nomor_surat_tugas']]);

        // Resolve dynamic records for seeding
        $jenisAudit = MasterJenisAudit::first();
        $auditee = MasterAuditee::first();
        $area = MasterArea::first();
        $users = MasterUser::take(2)->get();
        $koordinator = $users->first();
        $ketua = $users->last();

        $payload = [
            'tanggal_surat_tugas' => '2026-06-01',
            'nomor_surat_tugas' => 'ST/999/2026',
            'jenis_audit_id' => $jenisAudit->id,
            'area_id' => $area->id,
            'auditee' => $auditee->id,
            'ruang_lingkup' => ['Keuangan', 'Operasional'],
            'tanggal_audit_mulai' => '2026-06-05',
            'tanggal_audit_sampai' => '2026-06-15',
            'periode_awal' => '2026-01-01',
            'periode_akhir' => '2026-05-31',
            'koordinator_id' => $koordinator->id,
            'ketua_tim_id' => $ketua->id,
            'auditor' => [$koordinator->id, $ketua->id],
        ];

        // 3. Create (Store)
        $responseStore = $this->postJson('/api/v1/audit/perencanaan', $payload, $headers);
        $responseStore->assertStatus(201)
            ->assertJsonPath('success', true);
        
        $createdId = $responseStore->json('data.id');

        // 4. Read (Show)
        $this->getJson("/api/v1/audit/perencanaan/{$createdId}", $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.nomor_surat_tugas', 'ST/999/2026');

        // 5. Update
        $payloadUpdate = $payload;
        $payloadUpdate['nomor_surat_tugas'] = 'ST/999-UPDATED/2026';

        $this->putJson("/api/v1/audit/perencanaan/{$createdId}", $payloadUpdate, $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.nomor_surat_tugas', 'ST/999-UPDATED/2026');

        // 6. Index List
        $this->getJson('/api/v1/audit/perencanaan', $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // 7. Delete (Destroy)
        $this->deleteJson("/api/v1/audit/perencanaan/{$createdId}", [], $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // Verify it was deleted (404)
        $this->getJson("/api/v1/audit/perencanaan/{$createdId}", $headers)
            ->assertStatus(404);
    }
}
