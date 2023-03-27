<?php

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Order create($generalOrder_id, $generalOrder_id)
 * @see OrderService
*/

class OrderFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return OrderService::class;
    }
}
