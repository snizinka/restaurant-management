<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DishesController;
use App\Http\Controllers\Api\RestaurantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\DishCategoryController;

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
    Route::get('/orders', [OrderController::class, 'allOrders']);
    Route::post('/order/place', [OrderController::class, 'placeOrder']);
    Route::get('/order/status', [OrderController::class, 'checkOrder']);
    Route::get('/order/averagecost', [OrderController::class, 'averageOrderCost']);
    Route::get('/dishes', [DishesController::class, 'index']);
    Route::post('/dishes', [DishesController::class, 'store']);
    Route::get('/dishes/{id}', [DishesController::class, 'show']);
    Route::post('/dishes/{id}/update', [DishesController::class, 'update']);
    Route::delete('/dishes/{id}', [DishesController::class, 'destroy']);
    Route::get('/restaurants', [RestaurantController::class, 'restaurantList']);
    Route::get('/categories', [DishCategoryController::class, 'categories']);
});
