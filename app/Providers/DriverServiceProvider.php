<?php

namespace App\Providers;

use App\Models\Driver;
use App\Services\Driver\DriverService;
use Illuminate\Support\ServiceProvider;

class DriverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(DriverService::class, function () {
            return new DriverService(new Driver());
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
