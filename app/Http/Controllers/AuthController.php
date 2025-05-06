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
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'role' => 'required|in:approver,checker',
        ]);

        $guard = $request->role;

        // **Logout semua sesi sebelumnya sebelum login baru**
        Auth::guard('approver')->logout();
        Auth::guard('checker')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // **Coba login dengan user baru**
        if (Auth::guard($guard)->attempt(['username' => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate(); // Regenerasi session untuk keamanan
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

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // Tambahkan log untuk debugging
        Log::info('Mencoba login admin dengan username: ' . $request->username);

        // Attempt to authenticate the admin
        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Tambahkan log untuk sukses login
            Log::info('Login admin berhasil untuk: ' . $request->username);
            
            // Cek apakah admin benar-benar terautentikasi
            if (Auth::guard('admin')->check()) {
                Log::info('Admin terautentikasi: ' . Auth::guard('admin')->user()->name);
            } else {
                Log::warning('Admin tidak terautentikasi setelah login berhasil!');
            }

            // Redirect ke dashboard admin setelah login berhasil
            return redirect()->route('menu.dashboard_admin');
        }

        // Tambahkan log untuk gagal login
        Log::warning('Login admin gagal untuk username: ' . $request->username);

        // Authentication failed
        throw ValidationException::withMessages([
            'username' => [trans('auth.failed')],
        ]);
    }

    /**
     * Handle admin logout request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function adminLogout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to admin login page after logout
        return redirect()->route('admin.login');
    }
}
