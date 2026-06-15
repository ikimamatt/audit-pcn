<?php

namespace Tests\Feature;

use Tests\AuditApiTestCase;
use App\Models\Audit\PerencanaanAudit;
use App\Models\Models\Audit\PelaporanHasilAudit;
use App\Models\Audit\PelaporanTemuan;
use App\Models\PenutupLhaRekomendasi;
use App\Models\MasterData\MasterUser;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterJenisAudit;
use App\Models\MasterData\MasterArea;
use Illuminate\Http\UploadedFile;

class PenutupLhaApiControllerTest extends AuditApiTestCase
{
    protected $perencanaan;
    protected $pelaporan;
    protected $temuan;
    protected $auditor;
    protected $bcUser;
    protected $br1User;
    protected $br2User;

    protected function setUp(): void
    {
        parent::setUp();

        $jenisAudit = MasterJenisAudit::first();
        $auditee = MasterAuditee::first();
        $area = MasterArea::first();

        $this->auditor = MasterUser::where('username', 'dinar.afidah')->first();
        $this->bcUser = MasterUser::where('username', 'wahyu.kurniawan')->first();
        $this->br1User = MasterUser::where('username', 'asman.spi')->first();
        $this->br2User = MasterUser::where('username', 'agil.frassetyo')->first();

        // 1. Perencanaan
        $this->perencanaan = PerencanaanAudit::create([
            'tanggal_surat_tugas' => '2026-06-01',
            'nomor_surat_tugas' => 'ST/PENUTUP-TEST/2026',
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

        // 2. Pelaporan
        $this->pelaporan = PelaporanHasilAudit::create([
            'perencanaan_audit_id' => $this->perencanaan->id,
            'nomor_lha_lhk' => '001/LHA/POAUDIT/SPI.01.02/SPI.PCN.2026',
            'jenis_lha_lhk' => 'LHA',
            'kode_spi' => 'SPI.01.02',
            'jenis_audit_id' => $jenisAudit->id,
            'nomor_urut' => 1,
            'tahun' => 2026,
            'status_approval' => 'approved', // must be approved for temuan to be active
        ]);

        // 3. Temuan
        $this->temuan = PelaporanTemuan::create([
            'pelaporan_hasil_audit_id' => $this->pelaporan->id,
            'nomor_urut_iss' => 1,
            'hasil_temuan' => 'Temuan kas harian',
            'permasalahan' => 'Permasalahan kas harian',
            'penyebab' => 'Penyebab kas harian',
            'kriteria' => 'Kriteria kas harian',
            'nomor_iss' => 'ISS.001/PO PCN/SPI.01.02/01/01/2026',
            'tahun' => 2026,
            'kode_aoi_id' => 1,
            'kode_risk_id' => 1,
            'signifikan' => 'Medium',
            'status_approval' => 'approved',
        ]);
    }

    /**
     * Test Penutup LHA Rekomendasi CRUD and Tindak Lanjut indexing/creation.
     */
    public function test_penutup_lha_rekomendasi_crud_and_tindak_lanjut(): void
    {
        $headers = $this->auditorHeaders();

        $payload = [
            'pelaporan_isi_lha_id' => $this->temuan->id,
            'rekomendasi' => 'Rekomendasi kas harian',
            'rencana_aksi' => 'Rencana aksi kas harian',
            'eviden_rekomendasi' => 'Eviden kas harian',
            'pic_business_contact' => $this->bcUser->id,
            'pic_approval_1_spi' => $this->br1User->id,
            'pic_approval_2_spi' => $this->br2User->id,
            'target_waktu' => '2026-06-30',
        ];

        // 1. Create (Store)
        $responseStore = $this->postJson('/api/v1/audit/penutup-lha', $payload, $headers);
        if ($responseStore->status() !== 201) {
            dump('Penutup LHA Store Error:', $responseStore->json());
        }
        $responseStore->assertStatus(201)
            ->assertJsonPath('success', true);

        $createdId = $responseStore->json('data.id');

        // 2. Read (Show)
        $this->getJson("/api/v1/audit/penutup-lha/{$createdId}", $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.rekomendasi', 'Rekomendasi kas harian');

        // 3. Update
        $payloadUpdate = $payload;
        $payloadUpdate['rekomendasi'] = 'Rekomendasi kas harian - Updated';

        $this->putJson("/api/v1/audit/penutup-lha/{$createdId}", $payloadUpdate, $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.rekomendasi', 'Rekomendasi kas harian - Updated');

        // 4. Index List
        $this->getJson('/api/v1/audit/penutup-lha', $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // 5. Store Tindak Lanjut for this Recommendation
        $responseTindakLanjutStore = $this->postJson("/api/v1/audit/penutup-lha/{$createdId}/tindak-lanjut", [
            'real_waktu' => '2026-06-20',
            'komentar' => ['Eviden kas harian sudah dilengkapi.'],
            'file_eviden' => UploadedFile::fake()->create('eviden.pdf', 100, 'application/pdf'),
        ], $headers);

        if ($responseTindakLanjutStore->status() !== 201) {
            dump('Tindak Lanjut Store Error:', $responseTindakLanjutStore->json());
        }
        $responseTindakLanjutStore->assertStatus(201)
            ->assertJsonPath('success', true);

        // 6. Get Tindak Lanjut index for this Recommendation
        $this->getJson("/api/v1/audit/penutup-lha/{$createdId}/tindak-lanjut", $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.rekomendasi.id', $createdId);

        // 7. Approve Recommendation (Level 1)
        $this->postJson("/api/v1/audit/penutup-lha/{$createdId}/approval", [
            'action' => 'approve'
        ], $this->erpHeaders($this->br1User->nip, $this->br1User->email, $this->br1User->akses->nama_akses))
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals('approved_level1', PenutupLhaRekomendasi::find($createdId)->status_approval);

        // 8. Delete (Destroy)
        $this->deleteJson("/api/v1/audit/penutup-lha/{$createdId}", [], $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // Verify it was deleted
        $this->getJson("/api/v1/audit/penutup-lha/{$createdId}", $headers)
            ->assertStatus(404);
    }
}
