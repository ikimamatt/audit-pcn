<?php

namespace Tests\Feature;

use Tests\AuditApiTestCase;
use App\Models\Audit\PerencanaanAudit;
use App\Models\WalkthroughAudit;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\TodBpmAudit;
use App\Models\TodBpmEvaluasi;
use App\Models\MasterData\MasterUser;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterJenisAudit;
use App\Models\MasterData\MasterArea;

class TodBpmApiControllerTest extends AuditApiTestCase
{
    protected $perencanaan;
    protected $pka;
    protected $walkthrough;
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
            'nomor_surat_tugas' => 'ST/TOD-TEST/2026',
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
            'no_pka' => 'PKA/TOD-TEST/2026',
            'judul_pka' => 'PKA TOD Keuangan',
            'status_approval' => 'pending',
        ]);

        $this->walkthrough = WalkthroughAudit::create([
            'perencanaan_audit_id' => $this->perencanaan->id,
            'program_kerja_audit_id' => $this->pka->id,
            'planned_walkthrough_date' => '2026-06-06',
            'tanggal_walkthrough' => '2026-06-06',
            'auditee_nama' => 'Keuangan',
            'hasil_walkthrough' => 'Walkthrough kas harian.',
            'status_approval' => 'approved',
            'file_bpm' => 'bpm/test.pdf',
        ]);
    }

    /**
     * Test TOD BPM resource CRUD, approval, and evaluation.
     */
    public function test_tod_bpm_crud_approval_and_evaluation(): void
    {
        $headers = $this->auditorHeaders();

        $payload = [
            'perencanaan_audit_id' => $this->perencanaan->id,
            'judul_bpm' => 'Prosedur Penerimaan Kas Utama',
            'nama_bpo' => 'BPO Keuangan',
            'walkthrough_id' => $this->walkthrough->id,
            'hasil_evaluasi' => 'Cukup',
        ];

        // 1. Create (Store)
        $responseStore = $this->postJson('/api/v1/audit/tod-bpm', $payload, $headers);
        if ($responseStore->status() !== 201) {
            dump('TOD BPM Store Error:', $responseStore->json());
        }
        $responseStore->assertStatus(201)
            ->assertJsonPath('success', true);

        $createdId = $responseStore->json('data.id');

        // 2. Read (Show)
        $this->getJson("/api/v1/audit/tod-bpm/{$createdId}", $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.judul_bpm', 'Prosedur Penerimaan Kas Utama');

        // 3. Update
        $payloadUpdate = $payload;
        $payloadUpdate['judul_bpm'] = 'Prosedur Penerimaan Kas Utama - Updated';

        $this->putJson("/api/v1/audit/tod-bpm/{$createdId}", $payloadUpdate, $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.judul_bpm', 'Prosedur Penerimaan Kas Utama - Updated');

        // 4. Index List
        $this->getJson('/api/v1/audit/tod-bpm', $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // 5. Approve (Level 1)
        $this->postJson("/api/v1/audit/tod-bpm/{$createdId}/approval", [
            'action' => 'approve'
        ], $this->asKetuaTim($this->auditor))
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals('approved_level1', TodBpmAudit::find($createdId)->status_approval);

        // 6. Evaluasi Store
        $responseEval = $this->postJson('/api/v1/audit/tod-bpm-evaluasi', [
            'tod_bpm_audit_id' => $createdId,
            'hasil_evaluasi' => 'Evaluasi awal menunjukkan kontrol cukup memadai.'
        ], $headers);
        
        $responseEval->assertStatus(201)
            ->assertJsonPath('success', true);

        $evalId = $responseEval->json('data.id');

        // 7. Evaluasi Index
        $this->getJson("/api/v1/audit/tod-bpm/{$createdId}/evaluasi", $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // 8. Evaluasi Update
        $this->putJson("/api/v1/audit/tod-bpm-evaluasi/{$evalId}", [
            'hasil_evaluasi' => 'Evaluasi updated.'
        ], $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.hasil_evaluasi', 'Evaluasi updated.');

        // 9. Evaluasi Destroy
        $this->deleteJson("/api/v1/audit/tod-bpm-evaluasi/{$evalId}", [], $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // 10. Delete (Destroy)
        $this->deleteJson("/api/v1/audit/tod-bpm/{$createdId}", [], $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // Verify it was deleted
        $this->getJson("/api/v1/audit/tod-bpm/{$createdId}", $headers)
            ->assertStatus(404);
    }
}
