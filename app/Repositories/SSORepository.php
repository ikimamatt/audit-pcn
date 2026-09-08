<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;

class SSORepository
{
    /**
     * Check token validity to main SSO server
     *
     * @param string $token
     * @return array|null
     */
    public function checkTokenValidityToServer(string $token)
    {
        $response = Http::post(config('services.sso_pln_nd.check_token_url'), [
            'website_id' => config('services.sso_pln_nd.client_id'),
            'token' => $token,
        ]);

        return $response->json();
    }
}
