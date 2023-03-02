<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//Route::get('/login', [AuthController::class, 'create']);

//Route::post('/login', [AuthController::class, 'store'])->name('auth-login');
