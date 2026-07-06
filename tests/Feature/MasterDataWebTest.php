<?php

namespace Tests\Feature;

use Tests\AuditApiTestCase;
use App\Models\MasterData\MasterUser;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterKodeAoi;
use App\Models\MasterData\MasterKodeRisk;
use App\Models\MasterData\MasterArea;
use App\Models\MasterData\MasterJenisAudit;
use App\Models\MasterData\MasterSubBidang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MasterDataWebTest extends AuditApiTestCase
{
    protected $authorizedUser;
    protected $unauthorizedUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Retrieve seeded users
        $this->authorizedUser = MasterUser::where('username', 'asman.spi')->first(); // ASMAN SPI
        $this->unauthorizedUser = MasterUser::where('username', 'dinar.afidah')->first(); // AUDITOR
    }

    /**
     * Test authorization for master data routes.
     */
    public function test_authorization_rules(): void
    {
        // 1. Unauthenticated gets redirected to login
        $response = $this->get('/master/auditee');
        $response->assertRedirect('/login');

        // 2. Unauthorized role gets 403
        $this->actingAs($this->unauthorizedUser);
        $response = $this->get('/master/auditee');
        $response->assertStatus(403);

        // 3. Authorized role gets 200
        $this->actingAs($this->authorizedUser);
        $response = $this->get('/master/auditee');
        $response->assertStatus(200);
    }

    /**
     * Test MasterAuditee CRUD.
     */
    public function test_master_auditee_crud(): void
    {
        $this->actingAs($this->authorizedUser);

        // Index
        $response = $this->get('/master/auditee');
        $response->assertStatus(200)
            ->assertViewIs('master-data.auditee.index')
            ->assertViewHas('data');

        // Create form
        $response = $this->get('/master/auditee/create');
        $response->assertStatus(200)
            ->assertViewIs('master-data.auditee.create');

        // Store
        $uniqueKd = 'TEST_' . Str::random(5);
        $payload = [
            'kd_bidang' => $uniqueKd,
            'nama_bidang' => 'Test Bidang',
            'direktorat' => 'Direktorat Test',
            'divisi_cabang' => 'Divisi Test',
        ];
        $response = $this->post('/master/auditee', $payload);
        $response->assertRedirect(route('master.auditee.index'));
        $this->assertDatabaseHas('master_auditee', [
            'kd_bidang' => $uniqueKd,
            'nama_bidang' => 'Test Bidang',
        ]);

        $auditee = MasterAuditee::where('kd_bidang', $uniqueKd)->first();

        // Edit form
        $response = $this->get("/master/auditee/{$auditee->id}/edit");
        $response->assertStatus(200)
            ->assertViewIs('master-data.auditee.edit')
            ->assertViewHas('masterAuditee');

        // Update
        $payloadUpdate = $payload;
        $payloadUpdate['nama_bidang'] = 'Test Bidang Updated';
        $response = $this->put("/master/auditee/{$auditee->id}", $payloadUpdate);
        $response->assertRedirect(route('master.auditee.index'));
        $this->assertDatabaseHas('master_auditee', [
            'id' => $auditee->id,
            'nama_bidang' => 'Test Bidang Updated',
        ]);

        // Get Sub Bidang AJAX
        $response = $this->get("/master/auditee/{$auditee->id}/sub-bidang");
        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('bidang_id', $auditee->id);

        // Destroy
        $response = $this->delete("/master/auditee/{$auditee->id}");
        $response->assertRedirect(route('master.auditee.index'));
        $this->assertDatabaseMissing('master_auditee', ['id' => $auditee->id]);
    }

    /**
     * Test MasterKodeAoi CRUD.
     */
    public function test_master_kode_aoi_crud(): void
    {
        $this->actingAs($this->authorizedUser);

        // Index
        $response = $this->get('/master/kode-aoi');
        $response->assertStatus(200)
            ->assertViewIs('master-data.kode-aoi.index')
            ->assertViewHas('data');

        // Create form
        $response = $this->get('/master/kode-aoi/create');
        $response->assertStatus(200)
            ->assertViewIs('master-data.kode-aoi.create');

        // Store
        $uniqueKd = 'AOI_' . Str::random(5);
        $payload = [
            'kode_area_of_improvement' => $uniqueKd,
            'indikator_pengawasan' => 'Test Indikator',
            'deskripsi_area_of_improvement' => 'Test Deskripsi AOI',
        ];
        $response = $this->post('/master/kode-aoi', $payload);
        $response->assertRedirect(route('master.kode-aoi.index'));
        $this->assertDatabaseHas('master_kode_aoi', [
            'kode_area_of_improvement' => $uniqueKd,
            'indikator_pengawasan' => 'Test Indikator',
        ]);

        $aoi = MasterKodeAoi::where('kode_area_of_improvement', $uniqueKd)->first();

        // Edit form
        $response = $this->get("/master/kode-aoi/{$aoi->id}/edit");
        $response->assertStatus(200)
            ->assertViewIs('master-data.kode-aoi.edit')
            ->assertViewHas('masterKodeAoi');

        // Update
        $payloadUpdate = $payload;
        $payloadUpdate['indikator_pengawasan'] = 'Test Indikator Updated';
        $response = $this->put("/master/kode-aoi/{$aoi->id}", $payloadUpdate);
        $response->assertRedirect(route('master.kode-aoi.index'));
        $this->assertDatabaseHas('master_kode_aoi', [
            'id' => $aoi->id,
            'indikator_pengawasan' => 'Test Indikator Updated',
        ]);

        // Destroy
        $response = $this->delete("/master/kode-aoi/{$aoi->id}");
        $response->assertRedirect(route('master.kode-aoi.index'));
        $this->assertDatabaseMissing('master_kode_aoi', ['id' => $aoi->id]);
    }

    /**
     * Test MasterKodeRisk CRUD.
     */
    public function test_master_kode_risk_crud(): void
    {
        $this->actingAs($this->authorizedUser);

        // Index
        $response = $this->get('/master/kode-risk');
        $response->assertStatus(200)
            ->assertViewIs('master-data.kode-risk.index')
            ->assertViewHas('data');

        // Store
        $uniqueKd = 'RISK_' . Str::random(5);
        $payload = [
            'kelompok_risiko' => 'Kelompok Test',
            'kode_risiko' => $uniqueKd,
            'kelompok_risiko_detail' => 'Detail Test',
            'deskripsi_risiko' => 'Deskripsi Test',
        ];
        $response = $this->post('/master/kode-risk', $payload);
        $response->assertRedirect(route('master.kode-risk.index'));
        $this->assertDatabaseHas('master_kode_risk', [
            'kode_risiko' => $uniqueKd,
            'kelompok_risiko' => 'Kelompok Test',
        ]);

        $risk = MasterKodeRisk::where('kode_risiko', $uniqueKd)->first();

        // Update
        $payloadUpdate = $payload;
        $payloadUpdate['kelompok_risiko'] = 'Kelompok Test Updated';
        $response = $this->put("/master/kode-risk/{$risk->id}", $payloadUpdate);
        $response->assertRedirect(route('master.kode-risk.index'));
        $this->assertDatabaseHas('master_kode_risk', [
            'id' => $risk->id,
            'kelompok_risiko' => 'Kelompok Test Updated',
        ]);

        // Destroy
        $response = $this->delete("/master/kode-risk/{$risk->id}");
        $response->assertRedirect(route('master.kode-risk.index'));
        $this->assertDatabaseMissing('master_kode_risk', ['id' => $risk->id]);
    }

    /**
     * Test MasterUser CRUD and reset-password.
     */
    public function test_master_user_crud(): void
    {
        $this->actingAs($this->authorizedUser);

        // Index
        $response = $this->get('/master/user');
        $response->assertStatus(200)
            ->assertViewIs('master-data.user.index')
            ->assertViewHas('data');

        // Create form
        $response = $this->get('/master/user/create');
        $response->assertStatus(200)
            ->assertViewIs('master-data.user.create');

        $auditee = MasterAuditee::first();
        $area = MasterArea::first();
        $akses = \DB::table('master_akses_user')->first();

        // Store
        $uniqueNip = 'NIP_' . Str::random(5);
        $payload = [
            'nama' => 'Test User CRUD',
            'username' => 'test.user.crud',
            'nip' => $uniqueNip,
            'email' => 'test.user.crud@pcn.co.id',
            'no_telpon' => '08123456789',
            'jabatan' => 'Tester',
            'master_auditee_id' => $auditee->id,
            'master_area_id' => $area->id,
            'master_akses_user_id' => $akses->id,
            'password' => 'secret_password_123',
        ];
        $response = $this->post('/master/user', $payload);
        $response->assertRedirect(route('master.user.index'));
        $this->assertDatabaseHas('master_user', [
            'nip' => $uniqueNip,
            'nama' => 'Test User CRUD',
        ]);

        $user = MasterUser::where('nip', $uniqueNip)->first();

        // Edit form
        $response = $this->get("/master/user/{$user->id}/edit");
        $response->assertStatus(200)
            ->assertViewIs('master-data.user.edit')
            ->assertViewHas('masterUser');

        // Update
        $payloadUpdate = $payload;
        unset($payloadUpdate['password']); // exclude password from update payload usually
        $payloadUpdate['nama'] = 'Test User CRUD Updated';
        $response = $this->put("/master/user/{$user->id}", $payloadUpdate);
        $response->assertRedirect(route('master.user.index'));
        $this->assertDatabaseHas('master_user', [
            'id' => $user->id,
            'nama' => 'Test User CRUD Updated',
        ]);

        // Reset Password
        $response = $this->post("/master/user/{$user->id}/reset-password", [
            'password' => 'pln@nusadaya',
            'password_confirmation' => 'pln@nusadaya',
        ]);
        $response->assertRedirect(route('master.user.index'));
        $user->refresh();
        $this->assertTrue(Hash::check('pln@nusadaya', $user->password));

        // Destroy
        $response = $this->delete("/master/user/{$user->id}");
        $response->assertRedirect(route('master.user.index'));
        $this->assertDatabaseMissing('master_user', ['id' => $user->id]);
    }

    /**
     * Test MasterSubBidang CRUD (AJAX endpoints).
     */
    public function test_master_sub_bidang_ajax(): void
    {
        $this->actingAs($this->authorizedUser);

        $auditee = MasterAuditee::first();

        // Store Sub Bidang
        $response = $this->postJson('/master/sub-bidang', [
            'master_bidang_id' => $auditee->id,
            'nama' => 'Test Sub Bidang',
        ]);
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $subBidangId = $response->json('data.id');
        $this->assertDatabaseHas('master_sub_bidang', [
            'id' => $subBidangId,
            'nama' => 'Test Sub Bidang',
        ]);

        // Update Sub Bidang
        $response = $this->putJson("/master/sub-bidang/{$subBidangId}", [
            'nama' => 'Test Sub Bidang Updated',
        ]);
        $response->assertStatus(200)
            ->assertJsonPath('success', true);
        $this->assertDatabaseHas('master_sub_bidang', [
            'id' => $subBidangId,
            'nama' => 'Test Sub Bidang Updated',
        ]);

        // Destroy Sub Bidang
        $response = $this->deleteJson("/master/sub-bidang/{$subBidangId}");
        $response->assertStatus(200)
            ->assertJsonPath('success', true);
        $this->assertDatabaseMissing('master_sub_bidang', ['id' => $subBidangId]);
    }

    /**
     * Test MasterJenisAudit CRUD.
     */
    public function test_master_jenis_audit_crud(): void
    {
        $this->actingAs($this->authorizedUser);

        // Index
        $response = $this->get('/master/jenis-audit');
        $response->assertStatus(200)
            ->assertViewIs('master-data.jenis-audit.index')
            ->assertViewHas('data');

        // Create form
        $response = $this->get('/master/jenis-audit/create');
        $response->assertStatus(200)
            ->assertViewIs('master-data.jenis-audit.create');

        // Store
        $uniqueName = 'Jenis Audit Test ' . Str::random(5);
        $payload = [
            'nama_jenis_audit' => $uniqueName,
            'keterangan' => 'Keterangan Jenis Audit',
        ];
        $response = $this->post('/master/jenis-audit', $payload);
        $response->assertRedirect(route('master.jenis-audit.index'));
        $this->assertDatabaseHas('master_jenis_audit', [
            'nama_jenis_audit' => $uniqueName,
        ]);

        $jenis = MasterJenisAudit::where('nama_jenis_audit', $uniqueName)->first();

        // Edit form
        $response = $this->get("/master/jenis-audit/{$jenis->id}/edit");
        $response->assertStatus(200)
            ->assertViewIs('master-data.jenis-audit.edit')
            ->assertViewHas('masterJenisAudit');

        // Update
        $payloadUpdate = $payload;
        $payloadUpdate['nama_jenis_audit'] = $uniqueName . ' Updated';
        $response = $this->put("/master/jenis-audit/{$jenis->id}", $payloadUpdate);
        $response->assertRedirect(route('master.jenis-audit.index'));
        $this->assertDatabaseHas('master_jenis_audit', [
            'id' => $jenis->id,
            'nama_jenis_audit' => $uniqueName . ' Updated',
        ]);

        // Destroy
        $response = $this->delete("/master/jenis-audit/{$jenis->id}");
        $response->assertRedirect(route('master.jenis-audit.index'));
        $this->assertDatabaseMissing('master_jenis_audit', ['id' => $jenis->id]);
    }

    /**
     * Test MasterArea CRUD.
     */
    public function test_master_area_crud(): void
    {
        $this->actingAs($this->authorizedUser);

        // Index
        $response = $this->get('/master/area');
        $response->assertStatus(200)
            ->assertViewIs('master-data.area.index')
            ->assertViewHas('data');

        // Create form
        $response = $this->get('/master/area/create');
        $response->assertStatus(200)
            ->assertViewIs('master-data.area.create');

        // Store
        $region = \DB::table('master_region')->first();
        $kdRegion = $region ? $region->kd_region : '01';

        $uniqueKd = 'AREA_' . Str::random(5);
        $payload = [
            'kd_area' => $uniqueKd,
            'nama_area' => 'Test Area',
            'keterangan' => 'Keterangan Area',
            'kd_region' => $kdRegion,
        ];
        $response = $this->post('/master/area', $payload);
        $response->assertRedirect(route('master.area.index'));
        $this->assertDatabaseHas('master_area', [
            'kd_area' => $uniqueKd,
            'nama_area' => 'Test Area',
            'kd_region' => $kdRegion,
        ]);

        $area = MasterArea::where('kd_area', $uniqueKd)->first();

        // Edit form
        $response = $this->get("/master/area/{$area->id}/edit");
        $response->assertStatus(200)
            ->assertViewIs('master-data.area.edit')
            ->assertViewHas('masterArea');

        // Update
        $payloadUpdate = $payload;
        $payloadUpdate['nama_area'] = 'Test Area Updated';
        $response = $this->put("/master/area/{$area->id}", $payloadUpdate);
        $response->assertRedirect(route('master.area.index'));
        $this->assertDatabaseHas('master_area', [
            'id' => $area->id,
            'nama_area' => 'Test Area Updated',
            'kd_region' => $kdRegion,
        ]);

        // Destroy
        $response = $this->delete("/master/area/{$area->id}");
        $response->assertRedirect(route('master.area.index'));
        $this->assertSoftDeleted('master_area', ['id' => $area->id]);
    }
}
