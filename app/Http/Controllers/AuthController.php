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
        $this->logoutAllGuards($request);

        // **Cek apakah user dengan username tersebut ada**
        $userModel = $this->findUserByRole($guard, $request->username);

        // **Jika user tidak ditemukan**
        if (!$userModel) {
            return back()->with('error', 'Username tidak ditemukan untuk posisi ' . ucfirst($guard))
                        ->withInput($request->except('password'));
        }

        // **Coba login dengan user yang ditemukan**
        if (Auth::guard($guard)->attempt(['username' => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate(); // Regenerasi session untuk keamanan
            
            // **Set flag untuk notifikasi login berhasil SETELAH regenerate**
            session()->flash('login_success', true);
            
            // **Redirect ke dashboard spesifik berdasarkan role**
            return $this->redirectToDashboard($guard);
        } else {
            // **Jika password salah (user ada tapi password tidak cocok)**
            return back()->with('error', 'Password yang Anda masukkan salah')
                        ->withInput($request->except('password'));
        }
    }

    public function logout(Request $request)
    {
        $this->logoutAllGuards($request);

        // **Set flag untuk notifikasi logout berhasil**
        $request->session()->flash('logout_success', true);

        return redirect()->route('login');
    }

    /**
     * Method baru untuk handle unauthorized access
     * Dipanggil ketika user mengakses route yang tidak diizinkan
     */
    public function unauthorizedAccess(Request $request)
    {
        // Logout semua guards dan invalidate session
        $this->logoutAllGuards($request);
        
        // Set pesan error
        $request->session()->flash('error', 'Anda tidak memiliki akses ke halaman tersebut. Silakan login dengan role yang sesuai.');
        
        return redirect()->route('login');
    }

    /**
     * Helper method untuk logout semua guards
     */
    private function logoutAllGuards(Request $request)
    {
        // Logout semua guards
        Auth::guard('approver')->logout();
        Auth::guard('checker')->logout();
        Auth::guard('host')->logout();
        
        // Clear all session data completely
        $request->session()->flush(); // Menghapus semua data session
        $request->session()->invalidate(); // Invalidate session ID
        $request->session()->regenerateToken(); // Generate token baru
        
        // Clear any cached authentication data
        Auth::clearResolvedInstances();
    }

    /**
     * Helper method untuk mencari user berdasarkan role
     */
    private function findUserByRole($role, $username)
    {
        switch($role) {
            case 'approver':
                return \App\Models\Approver::where('username', $username)->first();
            case 'checker':
                return \App\Models\Checker::where('username', $username)->first();
            case 'host':
                return \App\Models\Host::where('username', $username)->first();
            default:
                return null;
        }
    }

    /**
     * Helper method untuk redirect ke dashboard sesuai role
     */
    private function redirectToDashboard($guard)
    {
        try {
            switch($guard) {
                case 'approver':
                case 'checker':
                    // Approver dan Checker menggunakan dashboard yang sama (method index)
                    return redirect()->route('dashboard');
                    
                case 'host':
                    return redirect()->route('host.dashboard');
                    
                default:
                    return redirect()->route('dashboard');
            }
        } catch (\Exception $e) {
            // Jika ada error dengan routing, fallback ke dashboard umum
            Log::error('Dashboard redirect error: ' . $e->getMessage());
            return redirect()->route('dashboard');
        }
    }
}