<?php

namespace Tests\Feature;

use Tests\AuditApiTestCase;
use App\Models\Audit\PerencanaanAudit;
use App\Models\WalkthroughAudit;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\MasterData\MasterUser;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterJenisAudit;
use App\Models\MasterData\MasterArea;

class WalkthroughApiControllerTest extends AuditApiTestCase
{
    protected $perencanaan;
    protected $pka;
    protected $auditor;

    protected function setUp(): void
    {
        parent::setUp();

        $jenisAudit = MasterJenisAudit::first();
        $auditee = MasterAuditee::first();
        $area = MasterArea::first();
        $this->auditor = MasterUser::where('username', 'dinar.afidah')->first();

        $this->perencanaan = PerencanaanAudit::create([
            'tanggal_surat_tugas' => '2026-06-01',
            'nomor_surat_tugas' => 'ST/WT-TEST/2026',
            'jenis_audit_id' => $jenisAudit->id,
            'jenis_audit' => $jenisAudit->nama_jenis_audit,
            'auditee_id' => $auditee->id,
            'area_id' => $area->id,
            'ruang_lingkup' => ['Keuangan'],
            'tanggal_audit_mulai' => '2026-06-05',
            'tanggal_audit_sampai' => '2026-06-15',
            'periode_audit' => '2026',
            'koordinator_id' => $this->auditor->id,
            'ketua_tim_id' => $this->auditor->id,
            'auditor' => ['Dinar Afidah - NIP: 01253007PST'],
        ]);

        $this->pka = ProgramKerjaAudit::create([
            'perencanaan_audit_id' => $this->perencanaan->id,
            'tanggal_pka' => '2026-06-05',
            'no_pka' => 'PKA/WT-TEST/2026',
            'judul_pka' => 'PKA WT Keuangan',
            'status_approval' => 'pending',
        ]);
    }

    /**
     * Test Walkthrough resource CRUD and approval.
     */
    public function test_walkthrough_crud_and_approval(): void
    {
        $headers = $this->auditorHeaders();
        $auditee = MasterAuditee::first();

        $payload = [
            'program_kerja_audit_id' => $this->pka->id,
            'planned_walkthrough_date' => '2026-06-06',
            'actual_walkthrough_date' => '2026-06-06',
            'auditee_id' => $auditee->id,
            'hasil_walkthrough' => 'Hasil WT yang solid.',
        ];

        // 1. Create (Store)
        $responseStore = $this->postJson('/api/v1/audit/walkthrough', $payload, $headers);
        $responseStore->assertStatus(201)
            ->assertJsonPath('success', true);

        $createdId = $responseStore->json('data.id');

        // 2. Read (Show)
        $this->getJson("/api/v1/audit/walkthrough/{$createdId}", $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.hasil_walkthrough', 'Hasil WT yang solid.');

        // 3. Update
        $payloadUpdate = $payload;
        $payloadUpdate['hasil_walkthrough'] = 'Hasil WT yang solid - Updated';

        $this->putJson("/api/v1/audit/walkthrough/{$createdId}", $payloadUpdate, $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.hasil_walkthrough', 'Hasil WT yang solid - Updated');

        // 4. Index List
        $this->getJson('/api/v1/audit/walkthrough', $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // 5. Approve (Level 1)
        $this->postJson("/api/v1/audit/walkthrough/{$createdId}/approval", [
            'action' => 'approve'
        ], $this->asKetuaTim($this->auditor))
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals('approved_level1', WalkthroughAudit::find($createdId)->status_approval);

        // 6. Delete (Destroy)
        $this->deleteJson("/api/v1/audit/walkthrough/{$createdId}", [], $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // Verify it was deleted
        $this->getJson("/api/v1/audit/walkthrough/{$createdId}", $headers)
            ->assertStatus(404);
    }
}
