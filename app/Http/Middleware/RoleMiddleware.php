<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        // Tentukan guard berdasarkan role
        $guard = $role === 'approver' ? 'approver' : 'checker';

        // Cek apakah user sudah login menggunakan guard yang sesuai
        if (!Auth::guard($guard)->check()) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai ' . ucfirst($role) . ' terlebih dahulu.');
        }

        return $next($request);
    }
}
