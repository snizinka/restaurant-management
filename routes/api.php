<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\DishCategoryController;
use App\Http\Controllers\Api\DishesController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\RestaurantController;
use Illuminate\Support\Facades\Route;


Route::get('/emai/verify/{id}', [\App\Http\Controllers\VerifyEmailController::class, 'verify'])->name('verify');

Route::get('/emai/reset/{id}', [\App\Http\Controllers\VerifyEmailController::class, 'reset'])->name('reset');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/register', [AuthController::class, 'register']);

Route::post('/reset', [AuthController::class, 'reset']);

Route::group(['middleware' => ['hasbearer']], function () {

    Route::group(['middleware' => ['isadmin']], function () {
        Route::post('/dishes', [DishesController::class, 'store']);
        Route::post('/dishes/{id}/update', [DishesController::class, 'update']);
        Route::delete('/dishes/{id}', [DishesController::class, 'destroy']);
        Route::post('/restaurants/{id}', [RestaurantController::class, 'updateRestaurant']);
        Route::delete('/restaurants/{id}', [RestaurantController::class, 'removeRestaurant']);
        Route::post('/restaurants', [RestaurantController::class, 'addRestaurant']);
        Route::post('/drivers/{id}', [OrderController::class, 'assignDriver']);
        Route::post('/drivers', [DriverController::class, 'addDriver']);
        Route::put('/drivers/{id}', [DriverController::class, 'updateDriver']);
        Route::delete('/drivers/{id}', [DriverController::class, 'removeDriver']);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/restaurant/{id}', [RestaurantController::class, 'dishesList']);
    Route::get('/cart', [CartController::class, 'getCart']);
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::delete('/cart/remove', [CartController::class, 'removeFromCart']);
    Route::get('/orders', [OrderController::class, 'allOrders']);
    Route::get('/orders/{id}', [OrderController::class, 'orderDetails']);
    Route::delete('/orders/{id}', [OrderController::class, 'removeOrder']);
    Route::post('/order/place', [OrderController::class, 'placeOrder']);
    Route::get('/order/status', [OrderController::class, 'checkOrder']);
    Route::get('/order/averagecost', [OrderController::class, 'averageOrderCost']);
    Route::get('/order/averagepaid', [OrderController::class, 'averageDriverPaid']);
    Route::get('/dishes', [DishesController::class, 'index']);
    Route::get('/dishes/{id}', [DishesController::class, 'show']);
    Route::get('/restaurants', [RestaurantController::class, 'restaurantList']);
    Route::get('/restaurants/{id}', [RestaurantController::class, 'getRestaurant']);
    Route::get('/categories', [DishCategoryController::class, 'categories']);
    Route::get('/drivers', [DriverController::class, 'getAllDrivers']);
    Route::get('/drivers/{id}', [DriverController::class, 'getDriver']);
    Route::put('/reset', [AuthController::class, 'confirmReset']);
});
