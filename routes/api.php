<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DishesController;
use App\Http\Controllers\Api\RestaurantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);

Route::post('/register', [AuthController::class, 'register']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/restaurant/{id}', [RestaurantController::class, 'dishesList']);
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::post('/cart/remove', [CartController::class, 'removeFromCart']);
    Route::post('/order/place', [OrderController::class, 'placeOrder']);
    Route::get('/order/status', [OrderController::class, 'checkOrder']);
    Route::get('/order/averagecost', [OrderController::class, 'averageOrderCost']);
    Route::resource('/dishes', DishesController::class);
});
