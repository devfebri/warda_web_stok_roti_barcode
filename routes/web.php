<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheesecakeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\RotiController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\BakerMiddleware;
use App\Http\Middleware\KepalaTokoMiddleware;
use App\Http\Middleware\PimpinanMiddleware;
use App\Http\Middleware\AdminMiddeleware;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/proses_login', [AuthController::class, 'proses_login'])->name('proses_login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/cheesecake/open/{id}', [CheesecakeController::class, 'open'])->name('cheesecakeopen');

// Debug route for testing statistics without auth



// Route khusus admin
Route::middleware(AdminMiddeleware::class)->prefix('admin')->name('admin_')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('rotis', RotiController::class);
    // Tambahkan route admin lain di sini jika perlu
});
Route::prefix('pimpinan')->middleware(PimpinanMiddleware::class)->name('pimpinan_')->group(function () {
    // Dashboard
    Route::get('/cheesecake', [CheesecakeController::class, 'index'])->name('cheesecake');
    Route::get('/cheesecake/statistics', [CheesecakeController::class, 'statistics'])->name('cheesecake_statistics');
    Route::get('/cheesecake/group/{groupId}', [CheesecakeController::class, 'getGroupDetails'])->name('cheesecake_group_details');
    Route::post('/cheesecake/store', [CheesecakeController::class, 'store'])->name('cheesecakestore');
    Route::get('/cheesecake/{id}/edit', [CheesecakeController::class, 'edit'])->name('cheesecakeedit');
    Route::get('/cheesecake/{id}/qrcode_show', [CheesecakeController::class, 'showQr'])->name('qrcode_show');
    Route::delete('/cheesecake/{id}', [CheesecakeController::class, 'destroy'])->name('cheesecakedelete');
    
    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::get('/laporan/harian', [LaporanController::class, 'harian'])->name('laporan_harian');
    Route::get('/laporan/bulanan', [LaporanController::class, 'bulanan'])->name('laporan_bulanan');
    Route::get('/laporan/tahunan', [LaporanController::class, 'tahunan'])->name('laporan_tahunan');
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan_export');
});

// Route laporan umum (accessible untuk semua role yang ada akses)
Route::middleware(['auth'])->group(function () {
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/harian', [LaporanController::class, 'harian'])->name('laporan.harian');
    Route::get('/laporan/bulanan', [LaporanController::class, 'bulanan'])->name('laporan.bulanan');
    Route::get('/laporan/tahunan', [LaporanController::class, 'tahunan'])->name('laporan.tahunan');
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');
});

Route::prefix('baker')->middleware(BakerMiddleware::class)->name('baker_')->group(function () {

    Route::get('/cheesecake', [CheesecakeController::class, 'index'])->name('cheesecake');
    Route::get('/cheesecake/statistics', [CheesecakeController::class, 'statistics'])->name('cheesecake_statistics');
    Route::get('/cheesecake/group/{groupId}', [CheesecakeController::class, 'getGroupDetails'])->name('cheesecake_group_details');
    Route::post('/cheesecake/store', [CheesecakeController::class, 'store'])->name('cheesecakestore');
    Route::get('/cheesecake/{id}/edit', [CheesecakeController::class, 'edit'])->name('cheesecakeedit');
    Route::get('/cheesecake/{id}/qrcode_show', [CheesecakeController::class, 'showQr'])->name('qrcode_show');
    Route::delete('/cheesecake/{id}', [CheesecakeController::class, 'destroy'])->name('cheesecakedelete');
    
});

Route::prefix('kepalatoko')->middleware(KepalaTokoMiddleware::class)->name('kepalatoko_')->group(function () {
    // Dashboard dan view produk
    Route::get('/cheesecake', [CheesecakeController::class, 'index'])->name('cheesecake');
    Route::get('/cheesecake/statistics', [CheesecakeController::class, 'statistics'])->name('cheesecake_statistics');
    Route::get('/cheesecake/group/{groupId}', [CheesecakeController::class, 'getGroupDetails'])->name('cheesecake_group_details');
    Route::post('/cheesecake/store', [CheesecakeController::class, 'store'])->name('cheesecakestore');
    Route::get('/cheesecake/{id}/edit', [CheesecakeController::class, 'edit'])->name('cheesecakeedit');
    Route::get('/cheesecake/{id}/qrcode_show', [CheesecakeController::class, 'showQr'])->name('qrcode_show');
    Route::delete('/cheesecake/{id}', [CheesecakeController::class, 'destroy'])->name('cheesecakedelete');
    
    // Transaksi Management
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi');
    Route::get('/transaksi/create', [TransaksiController::class, 'create'])->name('transaksi_create');
    Route::post('/transaksi/store', [TransaksiController::class, 'store'])->name('transaksi_store');
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi_show');
    Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy'])->name('transaksi_destroy');
    Route::get('/product/{id}', [TransaksiController::class, 'getProduct'])->name('get_product');
    Route::post('/scan-qr', [TransaksiController::class, 'getProductByQr'])->name('scan_qr');
    Route::get('/transaksi/statistics', [TransaksiController::class, 'statistics'])->name('kepalatoko_transaksi_statistics');
    Route::get('/transaksi/create-test-data', [TransaksiController::class, 'createTestData'])->name('create_test_data');
});

// Alias routes untuk karyawan (redirect ke kepalatoko)
Route::prefix('karyawan')->middleware(KepalaTokoMiddleware::class)->name('karyawan_')->group(function () {
    Route::get('/cheesecake', [CheesecakeController::class, 'index'])->name('cheesecake');
    Route::get('/cheesecake/statistics', [CheesecakeController::class, 'statistics'])->name('cheesecake_statistics');
    Route::get('/cheesecake/group/{groupId}', [CheesecakeController::class, 'getGroupDetails'])->name('cheesecake_group_details');
    Route::post('/cheesecake/store', [CheesecakeController::class, 'store'])->name('cheesecakestore');
    Route::get('/cheesecake/{id}/edit', [CheesecakeController::class, 'edit'])->name('cheesecakeedit');
    Route::get('/cheesecake/{id}/qrcode_show', [CheesecakeController::class, 'showQr'])->name('qrcode_show');
    Route::delete('/cheesecake/{id}', [CheesecakeController::class, 'destroy'])->name('cheesecakedelete');
    
    // Transaksi Management
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi');
    Route::get('/transaksi/create', [TransaksiController::class, 'create'])->name('transaksi_create');
    Route::post('/transaksi/store', [TransaksiController::class, 'store'])->name('transaksi_store');
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi_show');
    Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy'])->name('transaksi_destroy');
    Route::get('/product/{id}', [TransaksiController::class, 'getProduct'])->name('get_product');
    Route::post('/scan-qr', [TransaksiController::class, 'getProductByQr'])->name('scan_qr');
    Route::get('/transaksi/statistics', [TransaksiController::class, 'statistics'])->name('karyawan_transaksi_statistics');
});
