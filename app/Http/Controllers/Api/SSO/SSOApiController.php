<?php

namespace App\Http\Controllers\API\SSO;

use App\Http\Controllers\APIController;
use App\Models\MasterData\MasterUser;
use App\Repositories\SSORepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SSOApiController extends APIController
{
    /**
     * Used by SSO server to check if user exists in client application
     */
    public function checkUserExistsByNip(Request $request, SSORepository $ssoRepository)
    {
        $request->validate([
            'nip' => 'required',
            'token' => 'required',
        ]);

        $checkToken = $ssoRepository->checkTokenValidityToServer($request->token);

        if (!empty($checkToken['status']) && !empty($checkToken['data'])) {
            $user = MasterUser::where('nip', $request->nip)->first();
            return $this->sendSuccess($user, 'Hasil pencarian user.');
        }

        return $this->sendError($checkToken['message'] ?? 'Token tidak valid.');
    }

    /**
     * Callback used to authenticate the user and redirect to dashboard
     */
    public function login(Request $request, SSORepository $ssoRepository)
    {
        $request->validate([
            'nip' => 'required|exists:master_user,nip',
            'token' => 'required',
        ]);

        $checkToken = $ssoRepository->checkTokenValidityToServer($request->token);

        try {
            if (!empty($checkToken['status']) && !empty($checkToken['data'])) {
                $user = MasterUser::where('nip', $request->nip)->first();
                if ($user) {
                    Auth::login($user);
                    $request->session()->regenerate();

                    // Flash login info session for UI banner/alert
                    $request->session()->flash('login_success', [
                        'name' => $user->nama ?? 'User',
                        'role' => $user->akses?->nama_akses ?? 'User',
                        'time' => now()->format('H:i:s'),
                    ]);

                    // Redirect based on role (Auditee to pemantauan, SPI/BOD to /home)
                    $role = strtoupper(trim($user->akses?->nama_akses ?? ''));
                    if ($role === 'AUDITEE') {
                        return redirect()->intended(route('audit.pemantauan.index'));
                    }

                    return redirect()->intended(route('home'));
                }
            }
        } catch (\Exception $e) {
            Log::error('SSO Login Exception: ' . $e->getMessage());
        }

        return redirect()->away(config('services.sso_pln_nd.redirect_url'));
    }
}
