<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


//\Illuminate\Support\Facades\Auth::routes(['verify' => true]);
//Route::get('/login', [AuthController::class, 'create']);

//Route::post('/login', [AuthController::class, 'store'])->name('auth-login');


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
