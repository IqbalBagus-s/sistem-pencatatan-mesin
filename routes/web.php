<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AirDryerController;
use App\Http\Controllers\WaterChillerController;
use App\Http\Controllers\CompressorController;
use App\Http\Controllers\HopperController;
use App\Http\Controllers\DehumBahanController;


Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware(['auth:approver,checker'])->group(function () {
    // Route Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Route Mesin Air Dryer
    Route::get('/air-dryer', [AirDryerController::class, 'index'])->name('air-dryer.index');
    Route::get('/air-dryer/create', [AirDryerController::class, 'create'])->name('air-dryer.create');
    Route::post('/air-dryer', [AirDryerController::class, 'store'])->name('air-dryer.store');
    Route::get('/air-dryer/{id}/edit', [AirDryerController::class, 'edit'])->name('air-dryer.edit');
    Route::put('/air-dryer/{id}', [AirDryerController::class, 'update'])->name('air-dryer.update');
    Route::get('/air-dryer/{id}', [AirDryerController::class, 'show'])->name('air-dryer.show');
    Route::post('/air-dryer/{id}/approve', [AirDryerController::class, 'approve'])->name('air-dryer.approve');
    // Route download PDF
    Route::get('/air-dryer/{id}/download-pdf', [AirDryerController::class, 'downloadPdf'])->name('air-dryer.downloadPdf');

    // Route Mesin Water Chiller
    Route::get('/water-chiller', [WaterChillerController::class, 'index'])->name('water-chiller.index');
    Route::get('/water-chiller/create', [WaterChillerController::class, 'create'])->name('water-chiller.create');
    Route::post('/water-chiller', [WaterChillerController::class, 'store'])->name('water-chiller.store');
    Route::get('/water-chiller/{id}/edit', [WaterChillerController::class, 'edit'])->name('water-chiller.edit');
    Route::put('/water-chiller/{id}', [WaterChillerController::class, 'update'])->name('water-chiller.update');
    Route::get('/water-chiller/{id}', [WaterChillerController::class, 'show'])->name('water-chiller.show');
    Route::post('/water-chiller/{id}/approve', [WaterChillerController::class, 'approve'])->name('water-chiller.approve');
    // Route download PDF
    Route::get('/water-chiller/{id}/download-pdf', [WaterChillerController::class, 'downloadPdf'])->name('water-chiller.downloadPdf');
    
    // Route Mesin Compressor
    Route::get('/compressor', [CompressorController::class, 'index'])->name('compressor.index');
    Route::get('/compressor/create', [CompressorController::class, 'create'])->name('compressor.create');
    Route::post('/compressor', [CompressorController::class, 'store'])->name('compressor.store');
    Route::get('/compressor/{id}/edit', [CompressorController::class, 'edit'])->name('compressor.edit');
    Route::put('/compressor/{id}', [CompressorController::class, 'update'])->name('compressor.update');
    Route::get('/compressor/{id}', [CompressorController::class, 'show'])->name('compressor.show');
    Route::post('/compressor/{id}/approve', [CompressorController::class, 'approve'])->name('compressor.approve');
    // Route download PDF
    Route::get('/compressor/{id}/download-pdf', [CompressorController::class, 'downloadPdf'])->name('compressor.downloadPdf');

    // Route Mesin Hopper
    Route::get('/hopper', [HopperController::class, 'index'])->name('hopper.index');
    Route::get('/hopper/create', [HopperController::class, 'create'])->name('hopper.create');
    Route::post('/hopper', [HopperController::class, 'store'])->name('hopper.store');
    Route::get('/hopper/{id}/edit', [HopperController::class, 'edit'])->name('hopper.edit');
    Route::put('/hopper/{id}', [HopperController::class, 'update'])->name('hopper.update');
    Route::get('/hopper/{id}', [HopperController::class, 'show'])->name('hopper.show');
    Route::post('/hopper/{id}/approve', [HopperController::class, 'approve'])->name('hopper.approve');
    // Route download PDF
    Route::get('/hopper/{id}/download-pdf', [HopperController::class, 'downloadPdf'])->name('hopper.downloadPdf');

    // Route Mesin Dehum Bahan
    Route::get('/dehumbahan', [DehumBahanController::class, 'index'])->name('dehumbahan.index');
    Route::get('/dehumbahan/create', [DehumBahanController::class, 'create'])->name('dehumbahan.create');
    Route::post('/dehumbahan', [DehumBahanController::class, 'store'])->name('dehumbahan.store');
    Route::get('/dehumbahan/{id}/edit', [DehumBahanController::class, 'edit'])->name('dehumbahan.edit');
    Route::put('/dehumbahan/{id}', [DehumBahanController::class, 'update'])->name('dehumbahan.update');
    Route::get('/dehumbahan/{id}', [DehumBahanController::class, 'show'])->name('dehumbahan.show');
    Route::post('/dehumbahan/{id}/approve', [DehumBahanController::class, 'approve'])->name('dehumbahan.approve');
    // Route download PDF
    Route::get('/dehumbahan/{id}/download-pdf', [DehumBahanController::class, 'downloadPdf'])->name('dehumbahan.downloadPdf');
});
