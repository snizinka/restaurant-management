<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DishesResource;
use App\Models\Dish;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    use HttpResponses;
    public function dishesList(Request $request) {
        $dishes = Dish::where('restaurant_id', $request->id)->get();

        return DishesResource::collection($dishes);
    }
}
