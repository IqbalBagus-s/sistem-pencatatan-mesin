<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AirDryerController;
use App\Http\Controllers\WaterChillerController;
use App\Http\Controllers\CompressorController;
use App\Http\Controllers\HopperController;
use App\Http\Controllers\DehumBahanController;
use App\Http\Controllers\GilingController;
use App\Http\Controllers\AutoloaderController;
use App\Http\Controllers\DehumMatrasController;
use App\Http\Controllers\CapliningController;
use App\Http\Controllers\VacumCleanerController;
use App\Http\Controllers\SlittingController;
use App\Http\Controllers\CraneMatrasControler;
use App\Http\Controllers\ApproverController;
use App\Http\Controllers\CheckerController;
use App\Http\Controllers\FormController;

// Route Publik
Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Routing (Dinamis berdasarkan guard)
Route::middleware(['auth:approver,checker,host'])->group(function () {
    // Dashboard Dispatcher - akan mengarahkan ke dashboard sesuai role
    Route::get('/dashboard', function() {
        $user = Auth::user();
        $guard = Auth::getDefaultDriver();
        
        if ($guard == 'host') {
            return redirect()->route('host.dashboard');
        } else {
            return app(DashboardController::class)->index();
        }
    })->name('dashboard');
});

// Route khusus untuk Host
Route::middleware(['auth:host'])->prefix('host')->name('host.')->group(function () {
    // Dashboard Host
    Route::get('/dashboard', [DashboardController::class, 'hostDashboard'])->name('dashboard');
    
    // Perbaikan: Menghapus prefix 'menu.' pada nama route
    Route::resource('/approvers', ApproverController::class);
    Route::resource('/checkers', CheckerController::class);
    Route::resource('/forms', FormController::class);
});

// Route untuk User Approver dan Checker
Route::middleware(['auth:approver,checker'])->group(function () {
    // Daftar kontroler mesin dengan operasi CRUD yang sama
    $controllers = [
        'air-dryer' => AirDryerController::class,
        'water-chiller' => WaterChillerController::class,
        'compressor' => CompressorController::class,
        'hopper' => HopperController::class,
        'dehum-bahan' => DehumBahanController::class,
        'giling' => GilingController::class,
        'autoloader' => AutoloaderController::class,
        'dehum-matras' => DehumMatrasController::class,
        'caplining' => CapliningController::class,
        'vacuum-cleaner' => VacumCleanerController::class,
        'slitting' => SlittingController::class,
        'crane-matras' => CraneMatrasControler::class,
    ];
    
    // Buat resource route untuk semua controller
    foreach ($controllers as $route => $controller) {
        Route::resource($route, $controller);
        
        // Tambahkan route khusus untuk approve dan download PDF
        Route::post("/$route/{id}/approve", [$controller, 'approve'])->name("$route.approve");
        Route::get("/$route/{id}/review-pdf", [$controller, 'reviewPdf'])->name("$route.pdf");
        Route::get("/$route/{id}/download-pdf", [$controller, 'downloadPdf'])->name("$route.downloadPdf");
    }
});