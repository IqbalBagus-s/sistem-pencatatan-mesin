<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  ...$roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cek apakah user sudah login dengan salah satu guard
        $isLoggedIn = false;
        $currentGuard = null;
        
        foreach (['approver', 'checker', 'host'] as $guard) {
            if (Auth::guard($guard)->check()) {
                $isLoggedIn = true;
                $currentGuard = $guard;
                break;
            }
        }
        
        // Jika tidak ada yang login, redirect ke login
        if (!$isLoggedIn) {
            return redirect()->route('login');
        }
        
        // Jika user login tapi dengan role yang tidak diizinkan
        if (!in_array($currentGuard, $roles)) {
            // Logout user dan redirect ke login dengan pesan error
            Auth::guard($currentGuard)->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            $request->session()->flash('error', 'Anda tidak memiliki akses ke halaman tersebut. Silakan login dengan role yang sesuai.');
            
            return redirect()->route('login');
        }
        
        return $next($request);
    }
}