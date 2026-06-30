<?php

namespace App\Http\Middleware;

use App\Models\MasterData\MasterUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class GatewayValidation
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        // Bypass for direct ERP requests using ERP Token
        if ($request->hasHeader('X-ERP-Payload')) {
            return (new \App\Http\Middleware\ValidationERPToken())->handle($request, $next);
        }

        // Step 1: Check bypass using X-Internal-Key (for direct service-to-service calls)
        $internalKey = $request->header('X-Internal-Key');
        $expectedKey = config('erp.gateway_internal_key');

        if ($internalKey && hash_equals($expectedKey, $internalKey)) {
            // Internal service-to-service authentication is successful, bypass JWT
            return $next($request);
        }

        // Step 2: Validate Gateway Origin Header
        $gatewayService = $request->header('X-Gateway-Service');
        if ($gatewayService !== 'erp-api-gateway') {
            Log::warning('[GATEWAY] Request rejected. Missing or invalid X-Gateway-Service header.');
            return $this->unauthorized('Akses ditolak. Request harus melalui API Gateway.');
        }

        // Step 3: Extract JWT from Authorization header
        $authHeader = $request->header('Authorization', '');
        $token = str_starts_with($authHeader, 'Bearer ')
            ? substr($authHeader, 7)
            : null;

        if (!$token) {
            return $this->unauthorized('Token JWT tidak ditemukan.');
        }

        // Step 4: Validate JWT Signature (Defense in Depth against spoofing)
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return $this->unauthorized('Format JWT tidak valid.');
        }

        $secret = config('erp.gateway_jwt_secret');
        if (empty($secret)) {
            Log::error('[GATEWAY] JWT Secret is not configured in audit.');
            return $this->unauthorized('Konfigurasi keamanan server tidak lengkap.');
        }

        $headerAndPayload = $parts[0] . '.' . $parts[1];
        $signature = base64_decode(strtr($parts[2], '-_', '+/'));
        $expectedSignature = hash_hmac('sha256', $headerAndPayload, $secret, true);

        if (!hash_equals($signature, $expectedSignature)) {
            Log::warning('[GATEWAY] Invalid JWT Signature.');
            return $this->unauthorized('Token JWT tidak valid (Signature mismatch).');
        }

        $payload = json_decode(
            base64_decode(strtr($parts[1], '-_', '+/')),
            true
        );

        if (!$payload || !isset($payload['user_id'])) {
            return $this->unauthorized('Payload JWT tidak valid.');
        }

        // Step 5: Check JWT expiry
        if (isset($payload['exp']) && time() > $payload['exp']) {
            return $this->unauthorized('Token JWT sudah kadaluarsa.');
        }

        // Step 6: NIP Matching to local master_user
        $nip = $payload['nip'] ?? null;
        $localUser = null;
        $localRole = 'VIEW_ONLY';
        $canModify = false;

        if ($nip) {
            $localUser = MasterUser::with('akses')->where('nip', $nip)->first();
        }
        if (!$localUser && !empty($payload['email'])) {
            $localUser = MasterUser::with('akses')->where('email', $payload['email'])->first();
        }

        if ($localUser) {
            $localRole = $localUser->akses?->nama_akses ?? 'VIEW_ONLY';
            $nonModifyRoles = ['AUDITEE', 'VIEW BOD', 'VIEW_ONLY'];
            $canModify = !in_array($localRole, $nonModifyRoles);
        }

        // Step 7: Inject into request attributes
        $request->merge(['_erp_user' => $payload]);
        $request->attributes->set('erp_user_id', (int) ($payload['user_id'] ?? 0));
        $request->attributes->set('erp_nip', $nip ?? '');
        $request->attributes->set('erp_name', $payload['name'] ?? '');
        $request->attributes->set('erp_email', $payload['email'] ?? '');
        $request->attributes->set('erp_roles', (array) ($payload['roles'] ?? []));
        $request->attributes->set('erp_permissions', (array) ($payload['permissions'] ?? []));
        $request->attributes->set('erp_local_user', $localUser);
        $request->attributes->set('erp_local_role', $localRole);
        $request->attributes->set('erp_can_modify', $canModify);

        // Debug Log to verify JWT is successfully decoded and mapped
        Log::info('[GATEWAY | JWT] JWT successfully decoded and validated.', [
            'user_id'    => $payload['user_id'] ?? null,
            'nip'        => $nip,
            'name'       => $payload['name'] ?? null,
            'email'      => $payload['email'] ?? null,
            'local_role' => $localRole,
            'can_modify' => $canModify,
        ]);

        return $next($request);
    }

    private function unauthorized(string $message): Response
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 401);
    }
}
