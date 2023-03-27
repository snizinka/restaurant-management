<?php

namespace App\Providers;

use App\Models\OrderItem;
use App\Services\OrderItem\OrderItemService;
use Illuminate\Support\ServiceProvider;

class OrderItemServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(OrderItemService::class, function () {
            return new OrderItemService(new OrderItem());
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
