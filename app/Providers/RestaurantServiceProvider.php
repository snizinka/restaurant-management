<?php

namespace App\Providers;

use App\Models\Restaurant;
use App\Services\Restaurant\RestaurantService;
use Illuminate\Support\ServiceProvider;

class RestaurantServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(RestaurantService::class, function () {
            return new RestaurantService(new Restaurant());
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
