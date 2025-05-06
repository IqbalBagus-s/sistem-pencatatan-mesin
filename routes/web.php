<?php

use Illuminate\Support\Facades\Route;
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

// Admin Routes
Route::get('/admin-form', function () {
    return redirect('/admin-form/login');
})->name('admin-form');

Route::get('/admin-form/login', function () {
    return view('auth.admin-login');
})->name('admin.login');

Route::post('/admin-form/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');
Route::post('/admin-form/logout', [AuthController::class, 'adminLogout'])->name('admin.logout');

// Admin Dashboard (dengan middleware admin)
Route::middleware(['auth:admin'])->prefix('admin-form')->group(function () {
    Route::get('/dashboard-admin', [DashboardController::class, 'adminDashboard'])->name('menu.dashboard_admin');
    
    // Tambahkan route admin untuk menu items baru
    Route::resource('/approvers', ApproverController::class)->names('menu.approvers');
    Route::resource('/checkers', CheckerController::class)->names('menu.checkers');
    Route::resource('/forms', FormController::class)->names('menu.forms');
});

// Route yang memerlukan autentikasi
Route::middleware(['auth:approver,checker'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
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
    
    // Alternatif menggunakan Resource Controller (lebih disarankan):
    foreach ($controllers as $route => $controller) {
        Route::resource($route, $controller);
        
        // Tambahkan route khusus untuk approve dan download PDF
        Route::post("/$route/{id}/approve", [$controller, 'approve'])->name("$route.approve");
        Route::get("/$route/{id}/download-pdf", [$controller, 'downloadPdf'])->name("$route.downloadPdf");
    }
    
});