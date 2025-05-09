<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Approver;
use App\Models\Checker;
use App\Models\Form;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('menu.dashboard', compact('user'));
    }

    public function hostDashboard()
    {
        // Periksa apakah pengguna terautentikasi sebagai host
        if (!Auth::guard('host')->check()) {
            Log::warning('Akses dashboard host ditolak: tidak terautentikasi');
            return redirect()->route('host.login')
                ->with('error', 'Anda harus login sebagai host terlebih dahulu');
        }
        
        // Log untuk debugging
        Log::info('host mengakses dashboard: ' . Auth::guard('host')->user()->name);
        
        // Hitung jumlah approver dan checker yang aktif
        $approverCount = Approver::where('status', 'aktif')->count();
        $checkerCount = Checker::where('status', 'aktif')->count();
        $activeFormCount = Form::select('nomor_form')->distinct()->count();
        
        // Kirim data ke view
        return view('menu.dashboard_host', compact('approverCount', 'checkerCount', 'activeFormCount'));
    }
}

