<?php

namespace Tests\Feature;

use Tests\AuditApiTestCase;
use App\Models\Audit\PerencanaanAudit;
use App\Models\Models\Audit\ProgramKerjaAudit;
use App\Models\MasterData\MasterUser;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterJenisAudit;
use App\Models\MasterData\MasterArea;

class ProgramKerjaAuditApiControllerTest extends AuditApiTestCase
{
    protected $perencanaan;

    protected function setUp(): void
    {
        parent::setUp();

        $jenisAudit = MasterJenisAudit::first();
        $auditee = MasterAuditee::first();
        $area = MasterArea::first();
        $user = MasterUser::where('username', 'dinar.afidah')->first();

        $this->perencanaan = PerencanaanAudit::create([
            'tanggal_surat_tugas' => '2026-06-01',
            'nomor_surat_tugas' => 'ST/PKA-TEST/2026',
            'jenis_audit_id' => $jenisAudit->id,
            'jenis_audit' => $jenisAudit->nama_jenis_audit,
            'auditee_id' => $auditee->id,
            'area_id' => $area->id,
            'ruang_lingkup' => ['Keuangan'],
            'tanggal_audit_mulai' => '2026-06-05',
            'tanggal_audit_sampai' => '2026-06-15',
            'periode_audit' => '2026',
            'koordinator_id' => $user->id,
            'ketua_tim_id' => $user->id,
            'auditor' => ['Dinar Afidah - NIP: 01253007PST'],
        ]);
    }

    /**
     * Test PKA resource CRUD and custom endpoints.
     */
    public function test_pka_crud_and_custom_endpoints(): void
    {
        $headers = $this->auditorHeaders();
        $aoiId = \Illuminate\Support\Facades\DB::table('master_kode_aoi')->first()?->id;
        $riskId = \Illuminate\Support\Facades\DB::table('master_kode_risk')->first()?->id;

        $payload = [
            'perencanaan_audit_id' => $this->perencanaan->id,
            'tanggal_pka' => '2026-06-05',
            'no_pka' => 'PKA/PKA-TEST/2026',
            'judul_pka' => 'PKA Unit Keuangan',
            'proses_bisnis' => [
                [
                    'nama' => 'Prosedur Pencatatan Jurnal Kas',
                    'risiko' => [
                        [
                            'kode_risk_id' => $riskId,
                            'kontrol' => [
                                [
                                    'kode_aoi_id' => $aoiId,
                                    'nama_kontrol' => 'Review Kas Harian oleh Supervisor',
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'informasi_umum' => 'Info umum PKA Keuangan.',
            'kpi_tidak_tercapai' => 'Beberapa KPI kas terlambat.',
        ];

        // 1. Create (Store)
        $responseStore = $this->postJson('/api/v1/audit/pka', $payload, $headers);
        $responseStore->assertStatus(201)
            ->assertJsonPath('success', true);

        $createdId = $responseStore->json('data.id');

        // 2. Read (Show)
        $this->getJson("/api/v1/audit/pka/{$createdId}", $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.judul_pka', 'PKA Unit Keuangan');

        // 3. Update
        $payloadUpdate = $payload;
        $payloadUpdate['judul_pka'] = 'PKA Unit Keuangan Updated';

        $this->putJson("/api/v1/audit/pka/{$createdId}", $payloadUpdate, $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.judul_pka', 'PKA Unit Keuangan Updated');

        // 4. Index List
        $this->getJson('/api/v1/audit/pka', $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // 5. Check relations
        $this->getJson("/api/v1/audit/pka/{$createdId}/check-relations", $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // 6. Flat hierarchy
        $this->getJson("/api/v1/audit/pka/hierarki-flat/{$this->perencanaan->id}", $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // 7. Delete (Destroy)
        $this->deleteJson("/api/v1/audit/pka/{$createdId}", [], $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // Verify it was deleted
        $this->getJson("/api/v1/audit/pka/{$createdId}", $headers)
            ->assertStatus(404);
    }
}
