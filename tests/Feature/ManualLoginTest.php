<?php

namespace Tests\Feature;

use App\Models\MasterData\MasterAksesUser;
use App\Models\MasterData\MasterArea;
use App\Models\MasterData\MasterAuditee;
use App\Models\MasterData\MasterUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ManualLoginTest extends TestCase
{
    use DatabaseTransactions;

    private MasterUser $testUser;
    private string $testNip = 'TEST_MANUAL_NIP_1234';
    private string $testPassword = 'secret123';

    protected function setUp(): void
    {
        parent::setUp();

        $user = MasterUser::first();
        if ($user) {
            $this->testUser = $user;
            $this->testNip = $user->nip;
            $this->testUser->password = bcrypt($this->testPassword);
            $this->testUser->save();
        } else {
            $akses = MasterAksesUser::firstOrCreate(
                ['nama_akses' => 'SUPER ADMIN'],
                ['keterangan' => 'Super Administrator']
            );
            $auditee = MasterAuditee::first();
            $area = MasterArea::first();

            $this->testUser = MasterUser::firstOrCreate(
                ['nip' => $this->testNip],
                [
                    'nama' => 'Test Manual User',
                    'username' => 'test_manual_user',
                    'password' => bcrypt($this->testPassword),
                    'master_akses_user_id' => $akses->id,
                    'master_auditee_id' => $auditee?->id,
                    'master_area_id' => $area?->id,
                ]
            );
        }
    }

    /** @test */
    public function test_default_login_page_renders_sso_only_without_manual_form()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk dengan SSO NEXT');
        $response->assertDontSee('type="password"', false);
        $response->assertDontSee('name="nip"', false);
    }

    /** @test */
    public function test_manual_login_route_renders_manual_form()
    {
        $response = $this->get('/login/manual');

        $response->assertStatus(200);
        $response->assertSee('Silakan masuk menggunakan akun terdaftar');
        $response->assertSee('name="nip"', false);
        $response->assertSee('type="password"', false);
        $response->assertSee(route('login.manual'));
    }

    /** @test */
    public function test_manual_login_alias_route_renders_manual_form()
    {
        $response = $this->get('/login-manual');

        $response->assertStatus(200);
        $response->assertSee('Silakan masuk menggunakan akun terdaftar');
        $response->assertSee('name="nip"', false);
        $response->assertSee('type="password"', false);
    }

    /** @test */
    public function test_manual_login_redirects_if_already_authenticated()
    {
        Auth::login($this->testUser);

        $response = $this->get('/login/manual');

        $response->assertRedirect();
    }

    /** @test */
    public function test_manual_login_succeeds_with_valid_credentials()
    {
        $response = $this->post('/login/manual', [
            'nip' => $this->testNip,
            'password' => $this->testPassword,
        ]);

        $this->assertTrue(Auth::check());
        $this->assertEquals($this->testNip, Auth::user()->nip);
        $response->assertRedirect();
    }

    /** @test */
    public function test_manual_login_alias_succeeds_with_valid_credentials()
    {
        $response = $this->post('/login-manual', [
            'nip' => $this->testNip,
            'password' => $this->testPassword,
        ]);

        $this->assertTrue(Auth::check());
        $this->assertEquals($this->testNip, Auth::user()->nip);
        $response->assertRedirect();
    }

    /** @test */
    public function test_manual_login_fails_with_invalid_credentials()
    {
        $response = $this->post('/login/manual', [
            'nip' => $this->testNip,
            'password' => 'wrong-password-999',
        ]);

        $this->assertFalse(Auth::check());
        $response->assertSessionHasErrors('nip');
    }
}
