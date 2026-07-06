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
use App\Models\PenutupLhaRekomendasi;
use Illuminate\Support\Facades\DB;

class PersetujuanWorkflowTest extends AuditApiTestCase
{
    protected $ketuaUser;
    protected $koordUser;
    protected $auditeeUser;
    protected $br1User;
    protected $br2User;
    protected $perencanaan;
    protected $pka;
    protected $walkthrough;

    protected function setUp(): void
    {
        parent::setUp();

        // Get seeded users
        $this->ketuaUser = MasterUser::where('username', 'dinar.afidah')->first();
        $this->koordUser = MasterUser::where('username', 'agil.frassetyo')->first();
        $this->auditeeUser = MasterUser::where('username', 'wahyu.kurniawan')->first();
        $this->br1User = MasterUser::where('username', 'asman.spi')->first();
        $this->br2User = MasterUser::where('username', 'agil.frassetyo')->first(); // Use agil as BR2

        // Ensure roles are correctly loaded
        $this->ketuaUser->load('akses');
        $this->koordUser->load('akses');
        $this->auditeeUser->load('akses');
        $this->br1User->load('akses');
        $this->br2User->load('akses');

        // Create a test Perencanaan Audit
        $jenisAudit = MasterJenisAudit::first();
        $auditee = MasterAuditee::first();
        $area = MasterArea::first();

        $this->perencanaan = PerencanaanAudit::create([
            'tanggal_surat_tugas' => '2026-06-01',
            'nomor_surat_tugas' => 'ST/001/2026',
            'jenis_audit_id' => $jenisAudit->id,
            'jenis_audit' => $jenisAudit->nama_jenis_audit,
            'auditee_id' => $auditee->id,
            'area_id' => $area->id,
            'ruang_lingkup' => ['Keuangan', 'Operasional'],
            'tanggal_audit_mulai' => '2026-06-05',
            'tanggal_audit_sampai' => '2026-06-15',
            'periode_audit' => '2026-01-01 s/d 2026-05-31',
            'koordinator_id' => $this->koordUser->id,
            'ketua_tim_id' => $this->ketuaUser->id,
            'auditor' => ['Dinar Afidah - NIP: 01253007PST'],
        ]);

        // Create PKA
        $this->pka = ProgramKerjaAudit::create([
            'perencanaan_audit_id' => $this->perencanaan->id,
            'judul_pka' => 'PKA Test Keuangan',
            'tanggal_pka' => '2026-06-05',
            'no_pka' => 'PKA/001/2026',
            'status_approval' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Walkthrough
        $this->walkthrough = WalkthroughAudit::create([
            'perencanaan_audit_id' => $this->perencanaan->id,
            'program_kerja_audit_id' => $this->pka->id,
            'planned_walkthrough_date' => '2026-06-06',
            'actual_walkthrough_date' => '2026-06-06',
            'tanggal_walkthrough' => '2026-06-06',
            'auditee_nama' => $auditee->divisi ?? 'Keuangan',
            'hasil_walkthrough' => 'Walkthrough awal berhasil dilaksanakan.',
            'status_approval' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Test unauthenticated access.
     */
    public function test_unauthenticated_user_cannot_access_persetujuan_page(): void
    {
        // Web route
        $response = $this->get('/audit/persetujuan');
        $response->assertRedirect('/login');

        // API route
        $responseJson = $this->getJson('/api/v1/audit/persetujuan');
        $responseJson->assertStatus(401);
    }

    /**
     * Test Auditee accesses page.
     */
    public function test_auditee_sees_empty_dashboard_and_is_blocked_from_approving(): void
    {
        $headers = $this->auditeeHeaders();

        // API List pending items
        $response = $this->getJson('/api/v1/audit/persetujuan', $headers);
        $response->assertStatus(200);

        // Blocked from approving
        $responseProses = $this->postJson('/api/v1/audit/persetujuan', [
            'type' => 'walkthrough',
            'id' => $this->walkthrough->id,
            'action' => 'approve'
        ], $headers);

        $responseProses->assertStatus(403);
    }

    /**
     * Test Ketua Tim visibility on pending items.
     */
    public function test_ketua_tim_sees_only_pending_items_they_lead(): void
    {
        // Login as Ketua Tim
        $headers = $this->asKetuaTim($this->ketuaUser);

        // Fetch Web Dashboard (through session auth set in erpHeaders)
        $responseWeb = $this->get('/audit/persetujuan');
        $responseWeb->assertStatus(200);
        $responseWeb->assertSee('Walkthrough:');

        // Let's create another walkthrough where this user is NOT the Ketua Tim
        $otherKetua = MasterUser::where('username', 'asman.spi')->first();
        $otherPerencanaan = PerencanaanAudit::create([
            'tanggal_surat_tugas' => '2026-06-01',
            'nomor_surat_tugas' => 'ST/002/2026',
            'jenis_audit_id' => $this->perencanaan->jenis_audit_id,
            'jenis_audit' => $this->perencanaan->jenis_audit,
            'auditee_id' => $this->perencanaan->auditee_id,
            'area_id' => $this->perencanaan->area_id,
            'ruang_lingkup' => ['IT'],
            'tanggal_audit_mulai' => '2026-06-05',
            'tanggal_audit_sampai' => '2026-06-15',
            'periode_audit' => '2026',
            'koordinator_id' => $this->koordUser->id,
            'ketua_tim_id' => $otherKetua->id, // Different Ketua
            'auditor' => ['Asman - NIP: 85012345SPI'],
        ]);

        $otherPka = ProgramKerjaAudit::create([
            'perencanaan_audit_id' => $otherPerencanaan->id,
            'judul_pka' => 'PKA Test IT',
            'tanggal_pka' => '2026-06-05',
            'no_pka' => 'PKA/002/2026',
            'status_approval' => 'pending',
        ]);

        $otherWalkthrough = WalkthroughAudit::create([
            'perencanaan_audit_id' => $otherPerencanaan->id,
            'program_kerja_audit_id' => $otherPka->id,
            'planned_walkthrough_date' => '2026-06-06',
            'tanggal_walkthrough' => '2026-06-06',
            'auditee_nama' => 'IT',
            'hasil_walkthrough' => 'Walkthrough IT.',
            'status_approval' => 'pending',
        ]);

        // Get pending items on Web (uses PersetujuanService)
        // Dinar (current user) is Ketua for $this->walkthrough, but NOT for $otherWalkthrough.
        $this->actingAs($this->ketuaUser);
        $response = $this->get('/audit/persetujuan');
        $response->assertStatus(200);

        $pendingItems = $response->viewData('allPendingItems');

        $this->assertTrue($pendingItems->contains('id', $this->walkthrough->id));
        $this->assertFalse($pendingItems->contains('id', $otherWalkthrough->id));
    }

    /**
     * Test Koordinator visibility.
     */
    public function test_koordinator_sees_only_level1_approved_items_they_coordinate(): void
    {
        // 1. Walkthrough is still 'pending'. Koordinator should not see it in Web pending items
        // because it must be approved by Ketua Tim (Level 1) first.
        $this->actingAs($this->koordUser);
        $response = $this->get('/audit/persetujuan');
        $pendingItems = $response->viewData('allPendingItems');
        $this->assertFalse($pendingItems->contains('id', $this->walkthrough->id));

        // 2. Change walkthrough to approved_level1
        $this->walkthrough->update(['status_approval' => 'approved_level1']);

        // Now Koordinator should see it on the pending approval list
        $response2 = $this->get('/audit/persetujuan');
        $pendingItems2 = $response2->viewData('allPendingItems');
        $this->assertTrue($pendingItems2->contains('id', $this->walkthrough->id));
    }

    /**
     * Test transitions: pending -> approved_level1 -> approved.
     */
    public function test_approval_transitions_and_rules(): void
    {
        // 1. Koordinator tries to approve pending walkthrough -> should return error
        $responseK1 = $this->postJson('/api/v1/audit/persetujuan', [
            'type' => 'walkthrough',
            'id' => $this->walkthrough->id,
            'action' => 'approve'
        ], $this->asKoordinator($this->koordUser));
        $responseK1->assertStatus(403);
        $this->assertEquals('pending', $this->walkthrough->fresh()->status_approval);

        // 2. Ketua Tim approves pending walkthrough -> should success and status becomes approved_level1
        $responseK2 = $this->postJson('/api/v1/audit/persetujuan', [
            'type' => 'walkthrough',
            'id' => $this->walkthrough->id,
            'action' => 'approve'
        ], $this->asKetuaTim($this->ketuaUser));
        $responseK2->assertStatus(200);
        $this->assertEquals('approved_level1', $this->walkthrough->fresh()->status_approval);

        // 3. Ketua Tim tries to approve again -> should fail as it's already approved_level1
        $responseK3 = $this->postJson('/api/v1/audit/persetujuan', [
            'type' => 'walkthrough',
            'id' => $this->walkthrough->id,
            'action' => 'approve'
        ], $this->asKetuaTim($this->ketuaUser));
        $responseK3->assertStatus(403);

        // 4. Koordinator approves approved_level1 walkthrough -> should success and status becomes approved
        $responseK4 = $this->postJson('/api/v1/audit/persetujuan', [
            'type' => 'walkthrough',
            'id' => $this->walkthrough->id,
            'action' => 'approve'
        ], $this->asKoordinator($this->koordUser));
        if ($responseK4->status() !== 200) {
            dump('K4 Error:', $responseK4->json());
        }
        $responseK4->assertStatus(200);
        $this->assertEquals('approved', $this->walkthrough->fresh()->status_approval);
    }

    /**
     * Test rejection workflow.
     */
    public function test_rejection_validation_and_rollback(): void
    {
        // 1. Reject without reason -> fails with 422 because validation is caught by ApprovalHelper
        $response = $this->postJson('/api/v1/audit/persetujuan', [
            'type' => 'walkthrough',
            'id' => $this->walkthrough->id,
            'action' => 'reject'
        ], $this->asKetuaTim($this->ketuaUser));
        $response->assertStatus(422);

        // 2. Reject with short reason -> validation error (must be min 10 chars)
        $response2 = $this->postJson('/api/v1/audit/persetujuan', [
            'type' => 'walkthrough',
            'id' => $this->walkthrough->id,
            'action' => 'reject',
            'rejection_reason' => 'short'
        ], $this->asKetuaTim($this->ketuaUser));
        $response2->assertStatus(422);

        // 3. Reject with valid reason -> rolls back status to rejected_level1
        $response3 = $this->postJson('/api/v1/audit/persetujuan', [
            'type' => 'walkthrough',
            'id' => $this->walkthrough->id,
            'action' => 'reject',
            'rejection_reason' => 'Dokumen ini tidak valid dan butuh revisi total.'
        ], $this->asKetuaTim($this->ketuaUser));
        $response3->assertStatus(200);
        $this->assertEquals('rejected_level1', $this->walkthrough->fresh()->status_approval);
    }

    /**
     * Test Business Reviewers approval flow on recommendations.
     */
    public function test_business_reviewer_recommendation_flow(): void
    {
        $pelaporanId = (string) \Illuminate\Support\Str::uuid();
        $temuanId = (string) \Illuminate\Support\Str::uuid();
        $rekomendasiId = (string) \Illuminate\Support\Str::uuid();

        $aoiId = DB::table('master_kode_aoi')->first()?->id;
        $riskId = DB::table('master_kode_risk')->first()?->id;

        // 1. Create a Temuan and PenutupLhaRekomendasi
        DB::table('pelaporan_hasil_audit')->insert([
            'id' => $pelaporanId,
            'nomor_urut' => 99,
            'tahun' => 2026,
            'perencanaan_audit_id' => $this->perencanaan->id,
            'nomor_lha_lhk' => '001.LHA/PO/SPI.01.02/SPI.PCN/2026',
            'jenis_lha_lhk' => 'LHA',
            'kode_spi' => 'SPI.01.02',
            'status_approval' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pelaporan_temuan')->insert([
            'id' => $temuanId,
            'pelaporan_hasil_audit_id' => $pelaporanId,
            'nomor_urut_iss' => 1,
            'hasil_temuan' => 'Ketidakpatuhan Prosedur Kas Kecil',
            'permasalahan' => 'Kondisi Kas Kecil',
            'kriteria' => 'Kriteria Kas Kecil',
            'dampak_terjadi' => 'Akibat Kas Kecil',
            'penyebab' => 'Sebab Kas Kecil',
            'nomor_iss' => 'ISS.001/PO PCN/SPI.01.02/01/01/2026',
            'tahun' => 2026,
            'kode_aoi_id' => $aoiId,
            'kode_risk_id' => $riskId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('penutup_lha_rekomendasi')->insert([
            'id' => $rekomendasiId,
            'pelaporan_isi_lha_id' => $temuanId,
            'rekomendasi' => 'Melakukan rekonsiliasi kas secara harian.',
            'rencana_aksi' => 'Membuat form rekonsiliasi.',
            'eviden_rekomendasi' => 'Laporan rekonsiliasi harian.',
            'pic_rekomendasi' => 'Staf Akuntansi',
            'target_waktu' => '2026-06-30',
            'status_tindak_lanjut' => 'open',
            'status_approval' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $rekomendasi = PenutupLhaRekomendasi::find($rekomendasiId);

        // Assign PICs in pivot table
        DB::table('penutup_lha_rekomendasi_pic')->insert([
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'penutup_lha_rekomendasi_id' => $rekomendasiId,
                'master_user_id' => $this->br1User->id,
                'pic_type' => 'approval_1_spi',
                'created_at' => now(),
            ],
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'penutup_lha_rekomendasi_id' => $rekomendasiId,
                'master_user_id' => $this->br2User->id,
                'pic_type' => 'approval_2_spi',
                'created_at' => now(),
            ]
        ]);

        // 2. Business Reviewer 1 (ASMAN SPI) sees it in dashboard as pending
        $this->actingAs($this->br1User);
        $responseDashboard1 = $this->get('/audit/persetujuan');
        $pendingItems1 = $responseDashboard1->viewData('allPendingItems');
        $this->assertTrue($pendingItems1->contains('id', $rekomendasiId));

        // 3. Business Reviewer 2 (Superadmin) should NOT see it yet as it's not approved_level1
        $this->actingAs($this->br2User);
        $responseDashboard2 = $this->get('/audit/persetujuan');
        $pendingItems2 = $responseDashboard2->viewData('allPendingItems');
        $this->assertFalse($pendingItems2->contains('id', $rekomendasiId));

        // 4. BR1 approves Level 1
        $responseApproveLvl1 = $this->postJson("/api/v1/audit/tindak-lanjut/pemantauan/{$rekomendasiId}/status", [
            'action' => 'approve'
        ], $this->erpHeaders($this->br1User->nip, $this->br1User->email, $this->br1User->akses->nama_akses));
        $responseApproveLvl1->assertStatus(200);
        $this->assertEquals('approved_level1', $rekomendasi->fresh()->status_approval);
        $this->assertEquals('on_progress', $rekomendasi->fresh()->status_tindak_lanjut);

        // 5. BR2 (Superadmin) now sees it in dashboard
        $this->actingAs($this->br2User);
        $responseDashboard3 = $this->get('/audit/persetujuan');
        $pendingItems3 = $responseDashboard3->viewData('allPendingItems');
        $this->assertTrue($pendingItems3->contains('id', $rekomendasiId));

        // 6. BR2 approves Level 2
        $responseApproveLvl2 = $this->postJson("/api/v1/audit/tindak-lanjut/pemantauan/{$rekomendasiId}/status", [
            'action' => 'approve'
        ], $this->erpHeaders($this->br2User->nip, $this->br2User->email, $this->br2User->akses->nama_akses));
        $responseApproveLvl2->assertStatus(200);
        $this->assertEquals('approved', $rekomendasi->fresh()->status_approval);
        $this->assertEquals('closed', $rekomendasi->fresh()->status_tindak_lanjut);
    }
}
