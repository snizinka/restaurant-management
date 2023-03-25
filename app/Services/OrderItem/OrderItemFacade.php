<?php

namespace App\Services\OrderItem;

use App\Models\OrderItem;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Facade;
/**
 * @method static OrderItem create($dish_id, $order_id, $availability = "available")
 * @method static OrderItem increaseOrderCount($orderItem_id)
 * @method static  decreaseOrderCount($orderItem_id)
 * @method static Response delete($orderItem_id)
 * @see OrderItemService
 */
class OrderItemFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return OrderItemService::class;
    }
}
