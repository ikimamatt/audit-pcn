<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class RedirectBackToERPMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Intercept redirects if return_url is provided and validated
        if ($response instanceof RedirectResponse) {
            $returnUrl = $request->input('return_url') ?: $request->query('return_url');
            if ($returnUrl) {
                $allowedDomain = config('erp.allowed_domain');
                if ($allowedDomain) {
                    $expectedHost = parse_url($allowedDomain, PHP_URL_HOST);
                    $actualHost = parse_url($returnUrl, PHP_URL_HOST);
                    if ($expectedHost === $actualHost) {
                        // Preserve session success messages
                        $targetResponse = redirect()->to($returnUrl);
                        if (session()->has('success')) {
                            $targetResponse = $targetResponse->with('success', session('success'));
                        }
                        if (session()->has('error')) {
                            $targetResponse = $targetResponse->with('error', session('error'));
                        }
                        if (session()->has('nomor')) {
                            $targetResponse = $targetResponse->with('nomor', session('nomor'));
                        }
                        return $targetResponse;
                    }
                }
            }
        }

        return $response;
    }
}
