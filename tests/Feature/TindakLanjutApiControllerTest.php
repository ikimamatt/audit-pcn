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
use App\Models\WalkthroughAudit;
use App\Models\Models\Audit\ProgramKerjaAudit;
use Illuminate\Support\Facades\DB;

class TindakLanjutApiControllerTest extends AuditApiTestCase
{
    protected $perencanaan;
    protected $pelaporan;
    protected $temuan;
    protected $rekomendasi;
    protected $auditor;
    protected $br1User;
    protected $br2User;
    protected $bcUser;

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
            'nomor_surat_tugas' => 'ST/TL-TEST/2026',
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
            'nomor_lha_lhk' => '002/LHA/POAUDIT/SPI.01.02/SPI.PCN.2026',
            'jenis_lha_lhk' => 'LHA',
            'kode_spi' => 'SPI.01.02',
            'jenis_audit_id' => $jenisAudit->id,
            'nomor_urut' => 2,
            'tahun' => 2026,
            'status_approval' => 'approved',
        ]);

        // 3. Temuan
        $this->temuan = PelaporanTemuan::create([
            'pelaporan_hasil_audit_id' => $this->pelaporan->id,
            'nomor_urut_iss' => 1,
            'hasil_temuan' => 'Temuan kas harian TL',
            'permasalahan' => 'Permasalahan kas harian TL',
            'penyebab' => 'Penyebab kas harian TL',
            'kriteria' => 'Kriteria kas harian TL',
            'nomor_iss' => 'ISS.002/PO PCN/SPI.01.02/01/01/2026',
            'tahun' => 2026,
            'kode_aoi_id' => 1,
            'kode_risk_id' => 1,
            'signifikan' => 'Medium',
            'status_approval' => 'approved',
        ]);

        // 4. Rekomendasi
        $this->rekomendasi = PenutupLhaRekomendasi::create([
            'pelaporan_isi_lha_id' => $this->temuan->id,
            'rekomendasi' => 'Rekomendasi kas harian TL',
            'rencana_aksi' => 'Rencana aksi kas harian TL',
            'eviden_rekomendasi' => 'Eviden kas harian TL',
            'pic_rekomendasi' => 'Staf Keuangan',
            'target_waktu' => '2026-06-30',
            'status_tindak_lanjut' => 'open',
            'status_approval' => 'pending',
        ]);

        // Attach PICs
        $this->rekomendasi->picUsers()->attach([
            $this->bcUser->id => ['pic_type' => 'business_contact'],
            $this->br1User->id => ['pic_type' => 'approval_1_spi'],
            $this->br2User->id => ['pic_type' => 'approval_2_spi'],
        ]);
    }

    /**
     * Test Tindak Lanjut and Pemantauan APIs.
     */
    public function test_tindak_lanjut_and_pemantauan_apis(): void
    {
        $headers = $this->auditorHeaders();

        // 1. Select Nomor Surat Tugas
        $this->getJson('/api/v1/audit/tindak-lanjut/select-nomor-surat-tugas', $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // 2. Pemantauan Index
        $this->getJson('/api/v1/audit/tindak-lanjut/pemantauan?nomor_surat_tugas=ST/TL-TEST/2026', $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // 3. Detail
        $this->getJson("/api/v1/audit/tindak-lanjut/pemantauan/{$this->rekomendasi->id}", $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.rekomendasi.rekomendasi', 'Rekomendasi kas harian TL');

        // 4. Edit Pemantauan
        $this->putJson("/api/v1/audit/tindak-lanjut/pemantauan/{$this->rekomendasi->id}", [
            'rekomendasi' => 'Rekomendasi kas harian TL - Edited',
            'rencana_aksi' => 'Rencana aksi kas harian TL - Edited',
            'eviden_rekomendasi' => 'Eviden kas harian TL - Edited',
            'pic_rekomendasi' => 'Staf Keuangan - Edited',
            'target_waktu' => '2026-07-15',
        ], $headers)
            ->assertStatus(200)
            ->assertJsonPath('data.rekomendasi', 'Rekomendasi kas harian TL - Edited');

        // 5. Update Status (BR1 Approves Level 1)
        $this->postJson("/api/v1/audit/tindak-lanjut/pemantauan/{$this->rekomendasi->id}/status", [
            'action' => 'approve'
        ], $this->erpHeaders($this->br1User->nip, $this->br1User->email, $this->br1User->akses->nama_akses))
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals('approved_level1', $this->rekomendasi->fresh()->status_approval);

        // 6. Monitoring Index
        $this->getJson('/api/v1/audit/tindak-lanjut/monitoring', $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // 7. Progress Index
        $this->getJson('/api/v1/audit/tindak-lanjut/progress', $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    /**
     * Test Persetujuan Index and Process.
     */
    public function test_persetujuan_index_and_proses(): void
    {
        $headers = $this->auditorHeaders();

        // Create a pending Walkthrough
        $pka = ProgramKerjaAudit::create([
            'perencanaan_audit_id' => $this->perencanaan->id,
            'tanggal_pka' => '2026-06-05',
            'no_pka' => 'PKA/TL-P/2026',
            'judul_pka' => 'PKA TL Keuangan',
            'status_approval' => 'pending',
        ]);

        $wt = WalkthroughAudit::create([
            'perencanaan_audit_id' => $this->perencanaan->id,
            'program_kerja_audit_id' => $pka->id,
            'planned_walkthrough_date' => '2026-06-06',
            'tanggal_walkthrough' => '2026-06-06',
            'auditee_nama' => 'Keuangan',
            'hasil_walkthrough' => 'Hasil WT pending.',
            'status_approval' => 'pending',
        ]);

        // 1. Persetujuan Index
        $this->getJson('/api/v1/audit/persetujuan', $headers)
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // 2. Persetujuan Proses (Approve Walkthrough via general Persetujuan Proses endpoint)
        $this->postJson('/api/v1/audit/persetujuan', [
            'type' => 'walkthrough',
            'id' => $wt->id,
            'action' => 'approve',
        ], $this->asKetuaTim($this->auditor))
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals('approved_level1', $wt->fresh()->status_approval);
    }
}
