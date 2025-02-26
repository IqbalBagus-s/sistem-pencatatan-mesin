<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Approver;
use App\Models\Checker;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'role' => 'required|in:approver,checker',
        ]);

        // Cek peran (role) pengguna
        if ($request->role === 'approver') {
            $user = Approver::where('username', $request->username)->first();
            $guard = 'approver';
        } else {
            $user = Checker::where('username', $request->username)->first();
            $guard = 'checker';
        }

        // Verifikasi password
        if ($user && Hash::check($request->password, $user->password)) {
            Auth::guard($guard)->login($user);
            $request->session()->regenerate(); // Tambahkan ini
            return redirect()->route('dashboard')->with('success', 'Login berhasil!');

        }

        return back()->with('error', 'Username atau password salah.');
    }

    public function logout(Request $request)
    {
        // Tentukan guard berdasarkan user yang sedang login
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


