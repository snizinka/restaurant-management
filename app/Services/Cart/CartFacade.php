<?php

namespace App\Services\Cart;

use App\Models\Cart;
use Illuminate\Support\Facades\Facade;
/**
 * @method static Cart create($generalOrder_id)
 * @method static getCart()
 * @method static clearCart()
 * @see CartService
 */
class CartFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return CartService::class;
    }
}
