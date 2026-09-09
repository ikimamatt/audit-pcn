<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MasterData\MasterUser;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (Auth::check()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        return view('auth.login');
    }

    /**
     * Redirect langsung ke portal login SSO NEXT PLN Nusa Daya yang terdaftar.
     */
    public function redirectToSso()
    {
        $ssoUrl = config('services.sso_pln_nd.redirect_url', 'https://next.plnnusadaya.co.id/');

        return redirect()->away($ssoUrl);
    }

    /**
     * Handle an incoming authentication request.
     * Manual login dinonaktifkan (Only SSO).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        return redirect()->route('login.sso');
    }

    /**
     * Destroy an authenticated session.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Authenticate user from ERP token payload dynamically (Single Sign-On).
     */
    public function autologin(Request $request)
    {
        $payload = $request->query('payload');
        $signature = $request->query('signature');
        $domain = $request->query('domain');
        $redirectTo = $request->query('redirect_to', '/home');

        if (!$payload || !$signature || !$domain) {
            abort(400, 'Parameter autologin tidak lengkap.');
        }

        // 1. Validasi Domain
        if ($domain !== config('erp.allowed_domain')) {
            abort(403, 'Domain asal tidak diizinkan.');
        }

        // 2. Validasi Signature HMAC
        $secret = config('erp.shared_secret');
        $expectedSig = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expectedSig, $signature)) {
            abort(403, 'Tanda tangan autologin (signature) tidak valid.');
        }

        // 3. Decode Payload
        $json = base64_decode($payload, true);
        if ($json === false) {
            abort(400, 'Payload tidak valid.');
        }

        $data = json_decode($json, true);
        if (!is_array($data) || empty($data['expires_at'])) {
            abort(400, 'Struktur payload tidak valid.');
        }

        // 4. Validasi Expiry
        if (time() > (int) $data['expires_at']) {
            abort(401, 'Sesi autologin telah kadaluarsa.');
        }

        // 5. Cari User berdasarkan NIP
        $nip = $data['nip'] ?? null;
        if (!$nip) {
            abort(400, 'NIP tidak ditemukan dalam payload.');
        }

        $user = MasterUser::where('nip', $nip)->first();
        if (!$user) {
            abort(404, 'User dengan NIP terkait tidak terdaftar di sistem audit.');
        }

        // 6. Login User ke Web Session
        Auth::login($user);
        $request->session()->regenerate();

        $request->session()->flash('login_success', [
            'name' => $user->nama ?? 'User',
            'role' => $user->akses->nama_akses ?? 'User',
            'time' => now()->format('H:i:s')
        ]);

        // Cek redirect keselamatan: pastikan hanya redirect internal audit-pcn
        // (mencegah open redirect, path lokal harus diawali dengan '/')
        if (!str_starts_with($redirectTo, '/')) {
            $parsed = parse_url($redirectTo);
            $localPath = ($parsed['path'] ?? '/home') . (isset($parsed['query']) ? '?' . $parsed['query'] : '');
            return redirect()->to($localPath);
        }

        return redirect()->to($redirectTo);
    }
}
