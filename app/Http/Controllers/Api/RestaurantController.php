<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Resources\DishesResource;
use App\Http\Resources\RestaurantResource;
use App\Models\Dish;
use App\Models\Restaurant;
use App\Services\Restaurant\RestaurantFacade;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Exception;

class RestaurantController extends Controller
{
    use HttpResponses;
    public function dishesList(Request $request) {
        $dishes = Dish::where('restaurant_id', $request->id)->get(); // DISHES Service

        return DishesResource::collection($dishes);
    }

    public function restaurantList() {
        $restaurants = Restaurant::all();

        return RestaurantResource::collection($restaurants);
    }

    public function getRestaurant(string $id) {
        $restaurant = Restaurant::where('id', $id)->first();

        if($restaurant == null) {
            return [];
        }

        return new RestaurantResource($restaurant);
    }

    public function updateRestaurant(StoreRestaurantRequest $request, string $id) {
        $request->validated($request->all());
        $restaurant = RestaurantFacade::update($request, $id);

        return new RestaurantResource($restaurant);
    }

    public function removeRestaurant(string $id) {
        RestaurantFacade::delete($id);

        return true;
    }

    public function addRestaurant(StoreRestaurantRequest $request) {
        $request->validated($request->all());

        try {
            DB::beginTransaction();
            $restaurant = RestaurantFacade::create($request);
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }

        return new RestaurantResource($restaurant);
    }
}
