<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController; // Tambahkan ini
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;

// Halaman Awal
Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth:approver,checker'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
