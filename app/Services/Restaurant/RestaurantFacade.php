<?php

namespace App\Services\Restaurant;

use App\Models\Restaurant;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Facade;
/**
 * @method static Restaurant create($data)
 * @method static Restaurant update($data, $id)
 * @method static Response delete($id)
 * @see RestaurantService
 */
class RestaurantFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return RestaurantService::class;
    }
}
