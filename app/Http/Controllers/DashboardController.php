<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Approver;
use App\Models\Checker;
use App\Models\Form;
use App\Models\Activity;
use App\Models\AirDryerCheck;
use App\Models\WaterChillerCheck;
use App\Models\CompressorCheck;
use App\Models\HopperCheck;
use App\Models\DehumBahanCheck;
use App\Models\DehumMatrasDetail;
use App\Models\AutoloaderDetail;
use App\Models\GilingCheck;
use App\Models\CapliningCheck;
use App\Models\VacumCleanerCheck;
use App\Models\SlittingCheck;
use App\Models\CraneMatrasCheck;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Tentukan guard dan user yang sedang login
        $user = null;
        $currentGuard = null;
        
        foreach (['approver', 'checker'] as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                $currentGuard = $guard;
                break;
            }
        }
        
        // Jika tidak ada user yang login, redirect ke login
        if (!$user) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu');
        }
        
        // Hitung jumlah data yang belum disetujui untuk setiap mesin
        // Hanya untuk approver yang perlu melihat notifikasi
        $notificationCounts = [];
        
        if ($currentGuard === 'approver') {
            $notificationCounts = [
                'air_dryer' => AirDryerCheck::belumDisetujui()->count(),
                'water_chiller' => WaterChillerCheck::belumDisetujui()->count(),
                'compressor' => CompressorCheck::belumDisetujui()->count(),
                'hopper' => HopperCheck::belumDisetujui()->count(),
                'dehum_bahan' => DehumBahanCheck::belumDisetujui()->count(),
                'dehum_matras' => DehumMatrasDetail::countBelumDisetujuiGrouped(),
                'auto_loader' => AutoloaderDetail::countBelumDisetujuiGrouped(),
                'gilingan' => GilingCheck::belumDisetujui()->count(),
                'caplining' => CapliningCheck::belumDisetujui()->count(),
                'vacuum_cleaner' => VacumCleanerCheck::belumDisetujui()->count(),
                'slitting' => SlittingCheck::belumDisetujui()->count(),
                'crane_matras' => CraneMatrasCheck::belumDisetujui()->count(),
            ];
        }
        
        // Ambil aktivitas terbaru (5 terakhir)
        $recentActivities = Activity::recent(5)->get();
        
        return view('menu.dashboard', compact('user', 'notificationCounts', 'recentActivities', 'currentGuard'));
    }

    public function hostDashboard()
    {
        // Periksa apakah pengguna terautentikasi sebagai host
        if (!Auth::guard('host')->check()) {
            Log::warning('Akses dashboard host ditolak: tidak terautentikasi');
            return redirect()->route('login')
                ->with('error', 'Anda harus login sebagai host terlebih dahulu');
        }
        
        $user = Auth::guard('host')->user();
        
        // Log untuk debugging
        Log::info('host mengakses dashboard: ' . $user->name);
        
        // Hitung jumlah approver dan checker yang aktif
        $approverCount = Approver::where('status', 'aktif')->count();
        $checkerCount = Checker::where('status', 'aktif')->count();
        $activeFormCount = Form::select('nomor_form')->distinct()->count();
        
        // Ambil aktivitas terbaru (5 terakhir)
        $recentActivities = Activity::recent(5)->get();
        
        // Kirim data ke view
        return view('menu.dashboard_host', compact('user', 'approverCount', 'checkerCount', 'activeFormCount', 'recentActivities'));
    }
}