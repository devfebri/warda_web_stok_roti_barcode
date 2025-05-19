<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\BakerMiddleware;
use App\Http\Middleware\KepalaTokoMiddleware;
use App\Http\Middleware\PimpinanMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'index'])->middleware('guest')->name('login');
Route::post('/proses_login', [AuthController::class, 'proses_login'])->name('proses_login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('pimpinan')->middleware(PimpinanMiddleware::class)->name('pimpinan_')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::prefix('baker')->middleware(BakerMiddleware::class)->name('baker_')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::prefix('kepalatoko')->middleware(KepalaTokoMiddleware::class)->name('kepalatoko_')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
