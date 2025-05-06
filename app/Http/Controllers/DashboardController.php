<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Approver;
use App\Models\Checker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('menu.dashboard', compact('user'));
    }

    public function adminDashboard()
    {
        // Periksa apakah pengguna terautentikasi sebagai admin
        if (!Auth::guard('admin')->check()) {
            Log::warning('Akses dashboard admin ditolak: tidak terautentikasi');
            return redirect()->route('admin.login')
                ->with('error', 'Anda harus login sebagai admin terlebih dahulu');
        }
        
        // Log untuk debugging
        Log::info('Admin mengakses dashboard: ' . Auth::guard('admin')->user()->name);
        
        // Hitung jumlah approver dan checker yang aktif
        $approverCount = Approver::where('status', 'aktif')->count();
        $checkerCount = Checker::where('status', 'aktif')->count();
        
        
        // Kirim data ke view
        return view('menu.dashboard_admin', compact('approverCount', 'checkerCount'));
    }
}

