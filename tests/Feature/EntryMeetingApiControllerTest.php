<?php

namespace Tests\Feature;

use Tests\AuditApiTestCase;
use App\Models\Audit\PerencanaanAudit;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\EntryMeeting;
use App\Models\MasterData\MasterUser;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterJenisAudit;
use App\Models\MasterData\MasterArea;
use Illuminate\Http\UploadedFile;

class EntryMeetingApiControllerTest extends AuditApiTestCase
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
            'nomor_surat_tugas' => 'ST/ENTRY-TEST/2026',
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
            'no_pka' => 'PKA/ENTRY-TEST/2026',
            'judul_pka' => 'PKA Entry Keuangan',
            'status_approval' => 'pending',
        ]);
    }

    /**
     * Test Entry Meeting resource CRUD and approval.
     */
    public function test_entry_meeting_crud_and_approval(): void
    {
        $headers = $this->auditorHeaders();
        $auditee = MasterAuditee::first();

        $payload = [
            'program_kerja_audit_id' => $this->pka->id,
            'planned_meeting_date' => '2026-06-06',
            'actual_meeting_date' => '2026-06-06',
            'auditee_id' => $auditee->id,
            'file_undangan' => UploadedFile::fake()->create('undangan.pdf', 100, 'application/pdf'),
            'file_absensi' => UploadedFile::fake()->create('absensi.pdf', 100, 'application/pdf'),
        ];

        // 1. Create (Store)
        $responseStore = $this->postJson('/api/v1/audit/entry-meeting', $payload, $headers);
        if ($responseStore->status() !== 201) {
            dump('Entry Meeting Store Error:', $responseStore->json());
        }
        $responseStore->assertStatus(201)
            ->assertJsonPath('success', true);

        $createdId = $responseStore->json('data.id');

        // 2. Read (Show)
        $this->getJson("/api/v1/audit/entry-meeting/{$createdId}", $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.tanggal', '2026-06-06');

        // 3. Update (mimes: pdf, files are optional on update)
        $payloadUpdate = [
            'program_kerja_audit_id' => $this->pka->id,
            'planned_meeting_date' => '2026-06-07',
            'actual_meeting_date' => '2026-06-07',
            'auditee_id' => $auditee->id,
        ];

        $this->putJson("/api/v1/audit/entry-meeting/{$createdId}", $payloadUpdate, $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.tanggal', '2026-06-07');

        // 4. Index List
        $this->getJson('/api/v1/audit/entry-meeting', $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // 5. Approve (Level 1)
        $this->postJson("/api/v1/audit/entry-meeting/{$createdId}/approval", [
            'action' => 'approve'
        ], $this->asKetuaTim($this->auditor))
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals('approved_level1', EntryMeeting::find($createdId)->status_approval);

        // 6. Delete (Destroy)
        $this->deleteJson("/api/v1/audit/entry-meeting/{$createdId}", [], $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // Verify it was deleted
        $this->getJson("/api/v1/audit/entry-meeting/{$createdId}", $headers)
            ->assertStatus(404);
    }
}
