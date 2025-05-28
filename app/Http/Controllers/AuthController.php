<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validasi input dengan pesan error kustom
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'role' => 'required|in:approver,checker,host',
        ], [
            'username.required' => 'Username wajib diisi',
            'password.required' => 'Password wajib diisi',
            'role.required' => 'Posisi wajib dipilih',
            'role.in' => 'Posisi yang dipilih tidak valid',
        ]);

        $guard = $request->role;
        
        // **Logout semua sesi sebelumnya sebelum login baru**
        Auth::guard('approver')->logout();
        Auth::guard('checker')->logout();
        Auth::guard('host')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // **Cek apakah user dengan username tersebut ada**
        $userModel = null;
        switch($guard) {
            case 'approver':
                $userModel = \App\Models\Approver::where('username', $request->username)->first();
                break;
            case 'checker':
                $userModel = \App\Models\Checker::where('username', $request->username)->first();
                break;
            case 'host':
                $userModel = \App\Models\Host::where('username', $request->username)->first();
                break;
        }

        // **Jika user tidak ditemukan**
        if (!$userModel) {
            return back()->with('error', 'Username tidak ditemukan untuk posisi ' . ucfirst($guard))
                        ->withInput($request->except('password'));
        }

        // **Coba login dengan user yang ditemukan**
        if (Auth::guard($guard)->attempt(['username' => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate(); // Regenerasi session untuk keamanan
            
            // **Set flag untuk notifikasi login berhasil**
            $request->session()->flash('login_success', true);
            
            return redirect()->route('dashboard');
        } else {
            // **Jika password salah (user ada tapi password tidak cocok)**
            return back()->with('error', 'Password yang Anda masukkan salah')
                        ->withInput($request->except('password'));
        }
    }

    public function logout(Request $request)
    {
        if (Auth::guard('approver')->check()) {
            Auth::guard('approver')->logout();
        } elseif (Auth::guard('checker')->check()) {
            Auth::guard('checker')->logout();
        } elseif (Auth::guard('host')->check()) {
            Auth::guard('host')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // **Set flag untuk notifikasi logout berhasil**
        $request->session()->flash('logout_success', true);

        return redirect()->route('login');
    }
}