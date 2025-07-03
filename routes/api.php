<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RotiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return User::all();
});


Route::post('/proses_login_API', [AuthController::class, 'loginApi'])->name('proses_login');

// CRUD Roti
Route::get('/roti', [RotiController::class, 'indexApi'])->name('roti_index');      // List semua roti
Route::get('/roti/{id}', [RotiController::class, 'showApi'])->name('roti_show');   // Detail roti
Route::post('/roti', [RotiController::class, 'storeApi'])->name('roti_store');     // Tambah roti
Route::put('/roti/{id}', [RotiController::class, 'updateApi'])->name('roti_update'); // Update roti
Route::delete('/roti/{id}', [RotiController::class, 'destroyApi'])->name('roti_destroy'); // Hapus roti