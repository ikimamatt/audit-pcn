<?php

namespace Tests\Feature;

use Tests\AuditApiTestCase;
use App\Models\Audit\PerencanaanAudit;
use App\Models\Models\Audit\PelaporanHasilAudit;
use App\Models\Audit\PelaporanTemuan;
use App\Models\MasterData\MasterUser;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterJenisAudit;
use App\Models\MasterData\MasterArea;
use App\Models\MasterData\MasterKodeAoi;
use App\Models\MasterData\MasterKodeRisk;

class PelaporanApiControllerTest extends AuditApiTestCase
{
    protected $perencanaan;
    protected $auditor;
    protected $aoi;
    protected $risk;
    protected $jenisAudit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jenisAudit = MasterJenisAudit::first();
        $auditee = MasterAuditee::first();
        $area = MasterArea::first();
        $this->auditor = MasterUser::where('username', 'dinar.afidah')->first();

        // Ensure AOI and RISK codes are populated
        $this->aoi = MasterKodeAoi::first();
        if (!$this->aoi) {
            $this->aoi = MasterKodeAoi::create([
                'nama_aoi' => 'AOI Test',
                'keterangan' => 'AOI Keterangan Test',
            ]);
        }
        $this->risk = MasterKodeRisk::first();
        if (!$this->risk) {
            $this->risk = MasterKodeRisk::create([
                'nama_risk' => 'RISK Test',
                'keterangan' => 'RISK Keterangan Test',
            ]);
        }

        $this->perencanaan = PerencanaanAudit::create([
            'tanggal_surat_tugas' => '2026-06-01',
            'nomor_surat_tugas' => 'ST/LHA-TEST/2026',
            'jenis_audit_id' => $this->jenisAudit->id,
            'jenis_audit' => $this->jenisAudit->nama_jenis_audit,
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
    }

    /**
     * Test LHA and Temuan CRUD, generator, and approvals.
     */
    public function test_pelaporan_crud_and_approval(): void
    {
        $headers = $this->auditorHeaders();

        // 1. Generate LHA Number
        $responseLhaNum = $this->postJson('/api/v1/audit/pelaporan-hasil-audit/generate-nomor-lha-lhk', [
            'jenis_lha_lhk' => 'LHA',
            'jenis_audit_id' => $this->jenisAudit->id,
            'kode_spi' => 'SPI.01.02',
        ], $headers);
        $responseLhaNum->assertStatus(200);
        $lhaNumber = $responseLhaNum->json('data.nomor_lha_lhk');
        $this->assertNotEmpty($lhaNumber);

        // 2. Generate ISS Number
        $responseIssNum = $this->postJson('/api/v1/audit/pelaporan-hasil-audit/generate-nomor-iss', [
            'kode_aoi_id' => $this->aoi->id,
            'kode_risk_id' => $this->risk->id,
            'kode_spi' => 'SPI.01.02',
        ], $headers);
        $responseIssNum->assertStatus(200);
        $issNumber = $responseIssNum->json('data.nomor_iss');
        $this->assertNotEmpty($issNumber);

        // 3. Create Pelaporan Hasil Audit (Store)
        $payload = [
            'perencanaan_audit_id' => $this->perencanaan->id,
            'nomor_lha_lhk' => $lhaNumber,
            'jenis_lha_lhk' => 'LHA',
            'kode_spi' => 'SPI.01.02',
            'jenis_audit_id' => $this->jenisAudit->id,
            'hasil_temuan' => ['Temuan ke-1 kas keluar berlebih'],
            'kode_aoi_id' => [$this->aoi->id],
            'kode_risk_id' => [$this->risk->id],
            'nomor_iss' => [$issNumber],
            'nomor_urut_iss' => [1],
            'permasalahan' => ['Kas harian selisih lebih'],
            'penyebab' => ['Pencatatan terlambat'],
            'kriteria' => ['SOP Kas Kecil'],
            'dampak_terjadi' => ['Kerugian operasional'],
            'dampak_potensi' => ['Penyalahgunaan dana'],
            'signifikan' => ['Medium'],
        ];

        $responseStore = $this->postJson('/api/v1/audit/pelaporan-hasil-audit', $payload, $headers);
        if ($responseStore->status() !== 201) {
            dump('Pelaporan Hasil Audit Store Error:', $responseStore->json());
        }
        $responseStore->assertStatus(201)
            ->assertJsonPath('success', true);

        $createdId = $responseStore->json('data.id');

        // 4. Read Pelaporan (Show)
        $this->getJson("/api/v1/audit/pelaporan-hasil-audit/{$createdId}", $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.nomor_lha_lhk', $lhaNumber);

        // Get created temuan ID
        $temuan = PelaporanTemuan::where('pelaporan_hasil_audit_id', $createdId)->first();
        $this->assertNotNull($temuan);
        $temuanId = $temuan->id;

        // 5. Update Pelaporan
        $payloadUpdate = $payload;
        $payloadUpdate['temuan_id'] = [$temuanId];
        $payloadUpdate['hasil_temuan'] = ['Temuan ke-1 kas keluar berlebih - Updated'];

        $this->putJson("/api/v1/audit/pelaporan-hasil-audit/{$createdId}", $payloadUpdate, $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.nomor_lha_lhk', $lhaNumber);

        // Verify updated temuan
        $this->assertEquals('Temuan ke-1 kas keluar berlebih - Updated', $temuan->fresh()->hasil_temuan);

        // 6. Get Temuan for Pelaporan
        $this->getJson("/api/v1/audit/pelaporan-hasil-audit/{$createdId}/temuan", $headers)
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // 7. Get Temuan By ID
        $this->getJson("/api/v1/audit/pelaporan-hasil-audit/temuan/{$temuanId}", $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.id', $temuanId);

        // 8. Update Temuan Standalone
        $this->putJson("/api/v1/audit/pelaporan-hasil-audit/temuan/{$temuanId}", [
            'hasil_temuan' => 'Temuan standalone updated',
            'kode_aoi_id' => $this->aoi->id,
            'kode_risk_id' => $this->risk->id,
            'permasalahan' => 'Permasalahan standalone updated',
            'penyebab' => 'Penyebab standalone updated',
            'kriteria' => 'Kriteria standalone updated',
            'signifikan' => 'Tinggi',
        ], $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.hasil_temuan', 'Temuan standalone updated');

        // 9. Approve LHA (Level 1)
        $this->postJson("/api/v1/audit/pelaporan-hasil-audit/{$createdId}/approval", [
            'action' => 'approve'
        ], $this->asKetuaTim($this->auditor))
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals('approved_level1', PelaporanHasilAudit::find($createdId)->status_approval);

        // Approve LHA (Level 2 / Final)
        $this->postJson("/api/v1/audit/pelaporan-hasil-audit/{$createdId}/approval", [
            'action' => 'approve'
        ], $this->asKoordinator($this->auditor))
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals('approved', PelaporanHasilAudit::find($createdId)->status_approval);

        // 10. Get All Temuan for Penutup (requires approved LHA)
        $this->getJson('/api/v1/audit/pelaporan-hasil-audit/temuan-for-penutup', $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // 11. Delete LHA (Destroy)
        $this->deleteJson("/api/v1/audit/pelaporan-hasil-audit/{$createdId}", [], $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // Verify deleted
        $this->getJson("/api/v1/audit/pelaporan-hasil-audit/{$createdId}", $headers)
            ->assertStatus(404);
    }
}
