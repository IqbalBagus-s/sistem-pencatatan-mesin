<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cek apakah user sudah login dengan salah satu guard
        $isLoggedIn = false;
        $currentGuard = null;
        $userData = null;
        
        foreach (['approver', 'checker', 'host'] as $guard) {
            if (Auth::guard($guard)->check()) {
                $isLoggedIn = true;
                $currentGuard = $guard;
                $userData = Auth::guard($guard)->user();
                break;
            }
        }
        
        // Jika tidak ada yang login, redirect ke login
        if (!$isLoggedIn) {
            return redirect()->route('login')
                ->with('error', 'Anda harus login terlebih dahulu.');
        }
        
        // Jika user login tapi dengan role yang tidak diizinkan
        if (!in_array($currentGuard, $roles)) {
            // JANGAN logout user, redirect ke dashboard sesuai role mereka
            $dashboardRoute = $this->getDashboardRoute($currentGuard);
            
            return redirect()->route($dashboardRoute)
                ->with('warning', "Anda tidak memiliki akses ke halaman tersebut. Role Anda: {$currentGuard}");
        }
        
        return $next($request);
    }
    
    /**
     * Get dashboard route based on user role
     */
    private function getDashboardRoute($guard)
    {
        switch ($guard) {
            case 'host':
                return 'host.dashboard';
            case 'approver':
            case 'checker':
            default:
                return 'dashboard';
        }
    }
}