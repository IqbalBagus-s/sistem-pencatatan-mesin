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
    Route::get('/dehum-bahan', [DehumBahanController::class, 'index'])->name('dehum-bahan.index');
    Route::get('/dehum-bahan/create', [DehumBahanController::class, 'create'])->name('dehum-bahan.create');
    Route::post('/dehum-bahan', [DehumBahanController::class, 'store'])->name('dehum-bahan.store');
    Route::get('/dehum-bahan/{id}/edit', [DehumBahanController::class, 'edit'])->name('dehum-bahan.edit');
    Route::put('/dehum-bahan/{id}', [DehumBahanController::class, 'update'])->name('dehum-bahan.update');
    Route::get('/dehum-bahan/{id}', [DehumBahanController::class, 'show'])->name('dehum-bahan.show');
    Route::post('/dehum-bahan/{id}/approve', [DehumBahanController::class, 'approve'])->name('dehum-bahan.approve');
    // Route download PDF
    Route::get('/dehum-bahan/{id}/download-pdf', [DehumBahanController::class, 'downloadPdf'])->name('dehum-bahan.downloadPdf');
    
    // Route Mesin Giling
    Route::get('/giling', [GilingController::class, 'index'])->name('giling.index');
    Route::get('/giling/create', [GilingController::class, 'create'])->name('giling.create');
    Route::post('/giling', [GilingController::class, 'store'])->name('giling.store');
    Route::get('/giling/{id}/edit', [GilingController::class, 'edit'])->name('giling.edit');
    Route::put('/giling/{id}', [GilingController::class, 'update'])->name('giling.update');
    Route::get('/giling/{id}', [GilingController::class, 'show'])->name('giling.show');
    Route::post('/giling/{id}/approve', [GilingController::class, 'approve'])->name('giling.approve');
    // Route download PDF
    Route::get('/giling/{id}/download-pdf', [GilingController::class, 'downloadPdf'])->name('giling.downloadPdf');

    // Route Mesin Giling
    Route::get('/autoloader', [AutoloaderController::class, 'index'])->name('autoloader.index');
    Route::get('/autoloader/create', [AutoloaderController::class, 'create'])->name('autoloader.create');
    Route::post('/autoloader', [AutoloaderController::class, 'store'])->name('autoloader.store');
    Route::get('/autoloader/{id}/edit', [AutoloaderController::class, 'edit'])->name('autoloader.edit');
    Route::put('/autoloader/{id}', [AutoloaderController::class, 'update'])->name('autoloader.update');
    Route::get('/autoloader/{id}', [AutoloaderController::class, 'show'])->name('autoloader.show');
    Route::post('/autoloader/{id}/approve', [AutoloaderController::class, 'approve'])->name('autoloader.approve');
    // Route download PDF
    Route::get('/autoloader/{id}/download-pdf', [AutoloaderController::class, 'downloadPdf'])->name('autoloader.downloadPdf');
});