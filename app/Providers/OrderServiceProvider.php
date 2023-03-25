<?php

namespace App\Providers;

use App\Models\Order;
use App\Services\Order\OrderService;
use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OrderService::class, function () {
            return new OrderService(new Order());
        });
    }

    public function boot(): void
    {

    }
}
