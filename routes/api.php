<?php

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return User::all();
});


Route::post('/proses_login_API', [AuthController::class, 'loginApi'])->name('proses_login');
