<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AirDryerController;
use App\Http\Middleware\RoleMiddleware;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard 
Route::middleware(['auth:approver,checker'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Route yang bisa diakses oleh keduanya
    Route::get('/air-dryer', [AirDryerController::class, 'index'])->name('air-dryer.index');
    Route::get('/air-dryer/create', [AirDryerController::class, 'create'])->name('air-dryer.create');
    Route::post('/air-dryer', [AirDryerController::class, 'store'])->name('air-dryer.store');
    Route::get('/air-dryer/{id}/edit', [AirDryerController::class, 'edit'])->name('air-dryer.edit');
    Route::put('/air-dryer/{id}', [AirDryerController::class, 'update'])->name('air-dryer.update');
    Route::get('/air-dryer/{id}', [AirDryerController::class, 'show'])->name('air-dryer.show');
    Route::post('/air-dryer/{id}/approve', [AirDryerController::class, 'approve'])->name('air-dryer.approve');

    // Route download PDF
    Route::get('/air-dryer/{id}/download-pdf', [AirDryerController::class, 'downloadPdf'])->name('air-dryer.downloadPdf');
});
