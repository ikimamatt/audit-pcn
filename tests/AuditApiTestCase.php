<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\MasterData\MasterUser;
use Illuminate\Support\Facades\Auth;

abstract class AuditApiTestCase extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // If running on a clean/empty database, run seeders.
        // Otherwise, reuse existing seeded master data to make tests faster.
        if (MasterUser::count() === 0) {
            $this->seed(\Database\Seeders\MasterAksesUserSeeder::class);
            $this->seed(\Database\Seeders\MasterAuditeeSeeder::class);
            $this->seed(\Database\Seeders\MasterUnitSeeder::class);
            $this->seed(\Database\Seeders\MasterUserSeeder::class);
            $this->seed(\Database\Seeders\MasterJenisAuditSeeder::class);
        }
    }

    /**
     * Generate ERP authentication headers and mock config.
     */
    protected function erpHeaders(string $nip, string $email, string $role = 'AUDITOR', array $permissions = []): array
    {
        $sharedSecret = 'testing_secret_key_123';
        config(['erp.shared_secret' => $sharedSecret]);

        $allowedDomain = 'http://127.0.0.1:8000';
        config(['erp.allowed_domain' => $allowedDomain]);

        $payloadData = [
            'user_id' => 123,
            'nip' => $nip,
            'name' => 'ERP Test User',
            'email' => $email,
            'roles' => [$role],
            'permissions' => $permissions,
            'domain' => $allowedDomain,
            'expires_at' => time() + 3600,
        ];

        $payloadJson = json_encode($payloadData);
        $payloadBase64 = base64_encode($payloadJson);
        $signature = hash_hmac('sha256', $payloadBase64, $sharedSecret);

        // Also mock authentication inside Laravel guard if checking local session/web auth
        $localUser = MasterUser::where('nip', $nip)->first();
        if ($localUser) {
            $this->actingAs($localUser);
        }

        return [
            'X-ERP-Payload' => $payloadBase64,
            'X-ERP-Signature' => $signature,
            'X-ERP-Domain' => $allowedDomain,
            'Accept' => 'application/json',
        ];
    }

    /**
     * Authenticate as an Auditor (Dinar Afidah - NIP: 01253007PST)
     */
    protected function auditorHeaders(): array
    {
        return $this->erpHeaders('01253007PST', 'dinar.afidah@pcn.co.id', 'AUDITOR');
    }

    /**
     * Authenticate as an Auditee (Wahyu Kurniawan - NIP: 6724001OPS)
     */
    protected function auditeeHeaders(): array
    {
        return $this->erpHeaders('6724001OPS', 'wahyu.kurniawan@pcn.co.id', 'AUDITEE');
    }

    /**
     * Authenticate as a Ketua Tim (ASMAN SPI / Auditor - using dinar as ketua)
     */
    protected function asKetuaTim(MasterUser $user): array
    {
        return $this->erpHeaders($user->nip, $user->email, $user->akses->nama_akses);
    }

    /**
     * Authenticate as a Koordinator (using agil or specific koordinator user)
     */
    protected function asKoordinator(MasterUser $user): array
    {
        return $this->erpHeaders($user->nip, $user->email, $user->akses->nama_akses);
    }
}
