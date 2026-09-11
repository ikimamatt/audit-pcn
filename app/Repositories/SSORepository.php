<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SSORepository
{
    /**
     * Check token validity to main SSO server
     *
     * @param string $token
     * @return array
     */
    public function checkTokenValidityToServer(string $token): array
    {
        $url = config('services.sso_pln_nd.check_token_url', 'https://next.plnnusadaya.co.id/api/sso/check-token');
        $websiteId = config('services.sso_pln_nd.client_id');

        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->asJson()
                ->acceptJson()
                ->post($url, [
                    'website_id' => $websiteId,
                    'token'      => $token,
                ]);

            $json = $response->json();

            if (!is_array($json)) {
                Log::warning('SSO Server returned non-JSON response', [
                    'url'    => $url,
                    'status' => $response->status(),
                    'body'   => substr($response->body(), 0, 300),
                ]);

                return [
                    'status'  => false,
                    'message' => 'Respon dari server SSO tidak valid (HTTP ' . $response->status() . ').',
                    'data'    => false,
                ];
            }

            return $json;
        } catch (\Throwable $e) {
            Log::error('SSO checkTokenValidityToServer Exception: ' . $e->getMessage(), [
                'url' => $url,
            ]);

            return [
                'status'  => false,
                'message' => 'Gagal terhubung ke server SSO: ' . $e->getMessage(),
                'data'    => false,
            ];
        }
    }
}
