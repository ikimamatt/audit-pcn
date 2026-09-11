<?php

namespace Tests\Feature;

use App\Models\MasterData\MasterAksesUser;
use App\Models\MasterData\MasterUser;
use App\Repositories\SSORepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SSOIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    private MasterUser $testUser;
    private string $testNip = 'TEST_SSO_NIP_9988';

    protected function setUp(): void
    {
        parent::setUp();

        $user = MasterUser::first();
        if ($user) {
            $this->testUser = $user;
            $this->testNip = $user->nip;
        } else {
            $akses = MasterAksesUser::firstOrCreate(
                ['nama_akses' => 'SUPER ADMIN'],
                ['keterangan' => 'Super Administrator']
            );
            $auditee = \App\Models\MasterData\MasterAuditee::first();
            $area = \App\Models\MasterData\MasterArea::first();

            $this->testUser = MasterUser::firstOrCreate(
                ['nip' => $this->testNip],
                [
                    'nama' => 'Test SSO User',
                    'username' => 'test_sso_user',
                    'password' => bcrypt('secret123'),
                    'master_akses_user_id' => $akses->id,
                    'master_auditee_id' => $auditee?->id,
                    'master_area_id' => $area?->id,
                ]
            );
        }
    }

    /** @test */
    public function test_login_page_renders_sso_only_view()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk dengan SSO NEXT');
        $response->assertDontSee('type="password"', false);
        $response->assertDontSee('name="nip"', false);
    }

    /** @test */
    public function test_login_sso_route_redirects_to_registered_sso_portal()
    {
        $response = $this->get('/login/sso');

        $expectedUrl = config('services.sso_pln_nd.redirect_url');
        $this->assertStringStartsWith('https://next.plnnusadaya.co.id', $expectedUrl);
        $response->assertRedirect($expectedUrl);
    }

    /** @test */
    public function test_post_login_redirects_to_sso()
    {
        $response = $this->post('/login', [
            'nip' => $this->testNip,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('login.sso'));
    }

    /** @test */
    public function test_login_page_redirects_to_home_if_already_authenticated()
    {
        Auth::login($this->testUser);

        $response = $this->get('/login');
        $response->assertRedirect();
    }

    /** @test */
    public function test_check_user_exists_by_nip_returns_success_when_user_and_token_valid()
    {
        $this->mock(SSORepository::class, function ($mock) {
            $mock->shouldReceive('checkTokenValidityToServer')
                ->once()
                ->with('valid-token-xyz')
                ->andReturn([
                    'status' => true,
                    'data' => true,
                    'message' => 'Token valid',
                ]);
        });

        $response = $this->postJson('/api/sso/check-user', [
            'nip' => $this->testNip,
            'token' => 'valid-token-xyz',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'message' => 'Hasil pencarian user.',
        ]);
    }

    /** @test */
    public function test_check_user_exists_by_nip_returns_error_when_token_invalid()
    {
        $this->mock(SSORepository::class, function ($mock) {
            $mock->shouldReceive('checkTokenValidityToServer')
                ->once()
                ->with('invalid-token')
                ->andReturn([
                    'status' => false,
                    'message' => 'Token tidak valid.',
                ]);
        });

        $response = $this->postJson('/api/sso/check-user', [
            'nip' => $this->testNip,
            'token' => 'invalid-token',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => false,
            'message' => 'Token tidak valid.',
        ]);
    }

    /** @test */
    public function test_sso_callback_login_success_and_authenticates_user()
    {
        $this->mock(SSORepository::class, function ($mock) {
            $mock->shouldReceive('checkTokenValidityToServer')
                ->once()
                ->with('valid-token-abc')
                ->andReturn([
                    'status' => true,
                    'data' => true,
                    'message' => 'Token valid',
                ]);
        });

        $response = $this->get(route('login-sso', [
            'nip' => $this->testNip,
            'token' => 'valid-token-abc',
        ]));

        $this->assertTrue(Auth::check());
        $this->assertEquals($this->testNip, Auth::user()->nip);
        $response->assertRedirect(route('home'));
    }

    /** @test */
    public function test_sso_callback_login_fails_when_token_invalid()
    {
        $this->mock(SSORepository::class, function ($mock) {
            $mock->shouldReceive('checkTokenValidityToServer')
                ->once()
                ->with('invalid-token')
                ->andReturn([
                    'status' => false,
                    'message' => 'Token expired',
                ]);
        });

        $response = $this->get(route('login-sso', [
            'nip' => $this->testNip,
            'token' => 'invalid-token',
        ]));

        $this->assertFalse(Auth::check());
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('loginError');
    }
}
