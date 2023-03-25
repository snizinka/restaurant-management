<?php

namespace App\Services\Dish;

use App\Models\Dish;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Facade;
/**
 * @method static create($data)
 * @method static update($id, $request)
 * @method static Response delete($id)
 * @method static dishesFromRestaurant($id)
 * @see DishService
 */
class DishFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DishService::class;
    }
}
