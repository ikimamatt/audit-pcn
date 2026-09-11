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
        $nip = trim((string) $request->input('nip', ''));
        $token = trim((string) $request->input('token', ''));

        if (empty($nip) || empty($token)) {
            return $this->sendError('NIP dan Token wajib disertakan.', 422);
        }

        try {
            $checkToken = $ssoRepository->checkTokenValidityToServer($token);

            if (!empty($checkToken['status']) && !empty($checkToken['data'])) {
                $user = MasterUser::where('nip', $nip)->first();
                return $this->sendSuccess($user, 'Hasil pencarian user.');
            }

            return $this->sendError($checkToken['message'] ?? 'Token tidak valid.');
        } catch (\Throwable $e) {
            Log::error('SSO checkUserExistsByNip Exception: ' . $e->getMessage());
            return $this->sendError('Terjadi kesalahan pada verifikasi user SSO: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Callback used to authenticate the user and redirect to dashboard
     */
    public function login(Request $request, SSORepository $ssoRepository)
    {
        $nip = trim((string) $request->input('nip', ''));
        $token = trim((string) $request->input('token', ''));

        if (empty($nip) || empty($token)) {
            Log::warning('SSO Login: Parameter NIP atau Token tidak lengkap', [
                'nip' => $nip,
                'has_token' => !empty($token),
            ]);

            return redirect()->route('login')->with(
                'loginError',
                'Parameter login SSO tidak lengkap (NIP dan Token diperlukan).'
            );
        }

        try {
            $checkToken = $ssoRepository->checkTokenValidityToServer($token);

            if (empty($checkToken['status']) || empty($checkToken['data'])) {
                $errorMsg = $checkToken['message'] ?? 'Token SSO tidak valid atau telah kadaluarsa.';
                Log::warning('SSO Login: Verifikasi token ditolak oleh server SSO NEXT', [
                    'nip' => $nip,
                    'response' => $checkToken,
                ]);

                return redirect()->route('login')->with(
                    'loginError',
                    'Autentikasi SSO gagal: ' . $errorMsg
                );
            }

            $user = MasterUser::where('nip', $nip)->first();
            if (!$user) {
                Log::warning('SSO Login: NIP terverifikasi di SSO NEXT tetapi belum terdaftar di master_user Audit PCN', [
                    'nip' => $nip,
                ]);

                return redirect()->route('login')->with(
                    'loginError',
                    "Akun dengan NIP [{$nip}] belum terdaftar di sistem Audit PCN. Silakan hubungi Administrator SPI."
                );
            }

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
        } catch (\Throwable $e) {
            Log::error('SSO Login Exception: ' . $e->getMessage(), [
                'nip' => $nip,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')->with(
                'loginError',
                'Terjadi kesalahan saat memproses login SSO: ' . $e->getMessage()
            );
        }
    }
}
