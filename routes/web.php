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

// Route untuk handle unauthorized access
Route::get('/unauthorized', [AuthController::class, 'unauthorizedAccess'])->name('unauthorized');

// Dashboard Routing untuk Approver dan Checker (menggunakan method index yang sama)
Route::middleware(['check.role:approver,checker'])->group(function () {
    // Dashboard untuk Approver dan Checker - menggunakan method index
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Route khusus untuk Host
Route::middleware(['check.role:host'])->prefix('host')->name('host.')->group(function () {
    // Dashboard Host - menggunakan method hostDashboard
    Route::get('/dashboard', [DashboardController::class, 'hostDashboard'])->name('dashboard');
    
    // Resource routes untuk Host
    Route::resource('/approvers', ApproverController::class);
    Route::resource('/checkers', CheckerController::class);
    Route::resource('/forms', FormController::class);
});

// Route untuk User Approver dan Checker
Route::middleware(['check.role:approver,checker'])->group(function () {
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
    $modelBindings = [
        'air-dryer' => 'airDryer',
        'water-chiller' => 'waterChillerCheck',
        'compressor' => 'compressor',
        'hopper' => 'hopper',
        'dehum-bahan' => 'dehumBahan',
        'giling' => 'giling',
        'autoloader' => 'autoloader',
        'dehum-matras' => 'dehumMatras',
        'caplining' => 'caplining',
        'vacuum-cleaner' => 'vacumCleaner',
        'slitting' => 'slitting',
        'crane-matras' => 'craneMatras',
    ];
    foreach ($controllers as $route => $controller) {
        $param = $modelBindings[$route] ?? 'id';
        Route::resource($route, $controller)
            ->parameters([$route => $param]);
        Route::middleware(['check.role:approver'])->group(function () use ($route, $controller, $param) {
            Route::post("/$route/{{$param}}/approve", [$controller, 'approve'])->name("$route.approve");
            Route::get("/$route/{{$param}}/review-pdf", [$controller, 'reviewPdf'])->name("$route.pdf");
            Route::get("/$route/{{$param}}/download-pdf", [$controller, 'downloadPdf'])->name("$route.downloadPdf");
        });
    }
});