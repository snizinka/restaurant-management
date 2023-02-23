<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DishesController;
use App\Http\Controllers\Api\RestaurantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);

Route::post('/register', [AuthController::class, 'register']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/restaurant/{id}', [RestaurantController::class, 'dishesList']);
    Route::resource('/dishes', DishesController::class);
});
