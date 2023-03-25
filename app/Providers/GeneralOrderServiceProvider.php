<?php

namespace App\Providers;

use App\Models\GeneralOrder;
use App\Services\GeneralOrder\GeneralOrderService;
use Illuminate\Support\ServiceProvider;

class GeneralOrderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GeneralOrderService::class, function () {
            return new GeneralOrderService(new GeneralOrder());
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
