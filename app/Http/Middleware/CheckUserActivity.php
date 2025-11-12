<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (Auth::check()) {
            $lastActivity = Session::get('last_activity');
            $timeout = config('session.lifetime') * 60; // Convert minutes to seconds
            
            // Jika ada last_activity dan sudah timeout
            if ($lastActivity && (time() - $lastActivity) > $timeout) {
                // Logout user
                Auth::logout();
                Session::flush();
                
                // Redirect ke login dengan pesan timeout
                return redirect()->route('login')->with('warning', 'Session Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali.');
            }
            
            // Update last activity
            Session::put('last_activity', time());
        }
        
        return $next($request);
    }
}
