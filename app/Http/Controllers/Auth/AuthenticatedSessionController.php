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
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {

        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user(); // Get the authenticated MasterUser instance
        
        // Set login success message with user info
        $request->session()->flash('login_success', [
            'name' => $user->nama ?? 'User',
            'role' => $user->akses->nama_akses ?? 'User',
            'time' => now()->format('H:i:s')
        ]);

        if ($user && $user->akses) {
            switch ($user->akses->nama_akses) {
                case 'KSPI':
                case 'Auditor':
                    return redirect()->intended(RouteServiceProvider::HOME);
                case 'PIC Auditee':
                    return redirect()->intended('/audit/pemantauan-hasil-audit/tindak-lanjut'); // Assuming this route exists
                case 'Auditee':
                    return redirect()->intended(RouteServiceProvider::HOME); // Dashboard
                case 'BOD':
                    return redirect()->intended(route('audit.exit-meeting.chart'));
                default:
                    return redirect()->intended(RouteServiceProvider::HOME);
            }
        }

        return redirect()->intended(RouteServiceProvider::HOME);
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
}
