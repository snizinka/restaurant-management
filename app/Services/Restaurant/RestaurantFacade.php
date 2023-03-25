<?php

namespace App\Services\Restaurant;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Facade;
/**
 * @method static Restaurant create($data)
 * @method static Restaurant update($data, $id)
 * @method static delete($id)
 * @see RestaurantService
 */
class RestaurantFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return RestaurantService::class;
    }
}
