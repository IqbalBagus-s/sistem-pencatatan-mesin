<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'role' => 'required|in:approver,checker',
        ]);

        $guard = $request->role;
        
        if (Auth::guard($guard)->attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->route('dashboard');
        }

        return back()->with('error', 'Username atau password salah.');
    }

    public function logout(Request $request)
    {
        if (Auth::guard('approver')->check()) {
            Auth::guard('approver')->logout();
        } elseif (Auth::guard('checker')->check()) {
            Auth::guard('checker')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
