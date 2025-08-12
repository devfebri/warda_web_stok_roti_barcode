<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheesecakeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\RotiController;
use App\Http\Middleware\BakerMiddleware;
use App\Http\Middleware\KepalaTokoMiddleware;
use App\Http\Middleware\PimpinanMiddleware;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/proses_login', [AuthController::class, 'proses_login'])->name('proses_login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/cheesecake/open/{id}', [CheesecakeController::class, 'open'])->name('cheesecakeopen');

Route::prefix('pimpinan')->middleware(PimpinanMiddleware::class)->name('pimpinan_')->group(function () {
    // Dashboard
    Route::get('/cheesecake', [CheesecakeController::class, 'index'])->name('cheesecake');
    Route::get('/cheesecake/{id}/qrcode_show', [CheesecakeController::class, 'showQr'])->name('qrcode_show');
    
    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::get('/laporan/harian', [LaporanController::class, 'harian'])->name('laporan_harian');
    Route::get('/laporan/bulanan', [LaporanController::class, 'bulanan'])->name('laporan_bulanan');
    Route::get('/laporan/tahunan', [LaporanController::class, 'tahunan'])->name('laporan_tahunan');
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan_export');
});

Route::prefix('baker')->middleware(BakerMiddleware::class)->name('baker_')->group(function () {

    Route::get('/cheesecake', [CheesecakeController::class, 'index'])->name('cheesecake');
    Route::post('/cheesecake/store', [CheesecakeController::class, 'store'])->name('cheesecakestore');
    Route::get('/cheesecake/{id}/edit', [CheesecakeController::class, 'edit'])->name('cheesecakeedit');
    Route::get('/cheesecake/{id}/qrcode_show', [CheesecakeController::class, 'showQr'])->name('qrcode_show');
    Route::delete('/cheesecake/{id}', [CheesecakeController::class, 'destroy'])->name('cheesecakedelete');
    
});

Route::prefix('kepalatoko')->middleware(KepalaTokoMiddleware::class)->name('kepalatoko_')->group(function () {
    // Dashboard dan view produk
    Route::get('/cheesecake', [CheesecakeController::class, 'index'])->name('cheesecake');
    Route::get('/cheesecake/{id}/qrcode_show', [CheesecakeController::class, 'showQr'])->name('qrcode_show');
});
