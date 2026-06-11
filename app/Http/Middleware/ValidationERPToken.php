<?php

namespace App\Http\Middleware;

use App\Models\MasterData\MasterUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;


class ValidationERPToken
{
    public function handle(Request $request, Closure $next): Response
    {
        // Force response to be JSON in case of validation or other framework exceptions
        $request->headers->set('Accept', 'application/json');

        // ── STEP 1: Cek Header Lengkap ───────────────────────────────────────
        $payload = $request->header('X-ERP-Payload');
        $signature = $request->header('X-ERP-Signature');
        $domain = $request->header('X-ERP-Domain');

        if (!$payload || !$signature || !$domain) {
            $missing = array_values(array_filter([
                !$payload ? 'X-ERP-Payload' : null,
                !$signature ? 'X-ERP-Signature' : null,
                !$domain ? 'X-ERP-Domain' : null,
            ]));

            Log::warning('[VALIDATION | ERP | TOKEN] Header tidak lengkap.', [
                'missing' => $missing,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
            ]);

            return $this->unauthorized('Header ERP tidak lengkap: ' . implode(', ', $missing));
        }

        // ── STEP 2: Validasi Domain ──────────────────────────────────────────
        $allowedDomain = config('erp.allowed_domain');

        if ($domain !== $allowedDomain) {
            Log::warning('[VALIDATION | ERP | TOKEN] Domain tidak diizinkan.', [
                'received' => $domain,
                'expected' => $allowedDomain,
                'ip' => $request->ip(),
            ]);

            return $this->unauthorized('Domain tidak diizinkan.');
        }

        // ── STEP 3: Validasi HMAC-SHA256 Signature ───────────────────────────
        $secret = config('erp.shared_secret');
        $expectedSig = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expectedSig, $signature)) {
            Log::warning('[VALIDATION | ERP | TOKEN] Signature tidak valid.', [
                'domain' => $domain,
                'ip' => $request->ip(),
            ]);

            return $this->unauthorized('Signature tidak valid.');
        }

        // ── STEP 4: Decode Payload ───────────────────────────────────────────
        $json = base64_decode($payload, strict: true);

        if ($json === false) {
            return $this->unauthorized('Payload Base64 tidak valid.');
        }

        $data = json_decode($json, associative: true);

        if (!is_array($data)) {
            return $this->unauthorized('Payload JSON tidak valid.');
        }

        // ── STEP 5: Validasi Field Wajib di Payload ──────────────────────────
        $required = ['user_id', 'email', 'roles', 'permissions', 'domain', 'expires_at'];

        foreach ($required as $field) {
            if (!array_key_exists($field, $data)) {
                return $this->unauthorized("Field '{$field}' tidak ditemukan dalam payload.");
            }
        }

        // ── STEP 6: Cek Expiry ───────────────────────────────────────────────
        if (time() > (int) $data['expires_at']) {
            Log::info('[VALIDATION | ERP | TOKEN] Token kadaluarsa.', [
                'user_id' => $data['user_id'] ?? null,
                'expired_at' => date('Y-m-d H:i:s', $data['expires_at']),
            ]);

            return $this->unauthorized('Token sudah kadaluarsa.', 401);
        }

        // ── STEP 7: NIP Matching — Cari User di Audit PCN ────────────────────
        $nip = $data['nip'] ?? null;
        $localUser = null;
        $localRole = 'VIEW_ONLY';
        $canModify = false;

        if ($nip) {
            $localUser = MasterUser::with('akses')->where('nip', $nip)->first();
        }

        // Fallback: coba matching via email jika NIP tidak ada/ditemukan
        if (!$localUser && !empty($data['email'])) {
            $localUser = MasterUser::with('akses')->where('email', $data['email'])->first();
        }

        if ($localUser) {
            $localRole = $localUser->akses?->nama_akses ?? 'VIEW_ONLY';

            // Tentukan apakah user bisa melakukan modifikasi data
            // AUDITEE dan VIEW BOD tidak bisa modify, sesuai logic AuthHelper::canModifyData()
            $nonModifyRoles = ['AUDITEE', 'VIEW BOD', 'VIEW_ONLY'];
            $canModify = !in_array($localRole, $nonModifyRoles);
        }

        // ── STEP 8: Inject Data ERP ke Request ───────────────────────────────
        $request->merge(['_erp_user' => $data]);

        $request->attributes->set('erp_user_id', (int) ($data['user_id'] ?? 0));
        $request->attributes->set('erp_nip', $nip ?? '');
        $request->attributes->set('erp_name', $data['name'] ?? '');
        $request->attributes->set('erp_email', $data['email'] ?? '');
        $request->attributes->set('erp_roles', (array) ($data['roles'] ?? []));
        $request->attributes->set('erp_permissions', (array) ($data['permissions'] ?? []));
        $request->attributes->set('erp_domain', $data['domain'] ?? '');
        $request->attributes->set('erp_local_user', $localUser);
        $request->attributes->set('erp_local_role', $localRole);
        $request->attributes->set('erp_can_modify', $canModify);

        Log::debug('[VALIDATION | ERP | TOKEN] Request valid.', [
            'erp_user_id' => $data['user_id'],
            'erp_email' => $data['email'],
            'nip' => $nip,
            'local_role' => $localRole,
            'can_modify' => $canModify,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        return $next($request);
    }

    /**
     * Response 401 Unauthorized dengan format JSON standar.
     */
    private function unauthorized(string $message, int $code = 401): Response
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => $code,
        ], $code);
    }
}
