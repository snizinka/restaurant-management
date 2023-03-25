<?php

namespace App\Providers;

use App\Models\Dish;
use App\Services\Dish\DishService;
use Illuminate\Support\ServiceProvider;

class DishServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(DishService::class, function () {
            return new DishService(new Dish());
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
