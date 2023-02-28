<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Resources\DishesResource;
use App\Http\Resources\RestaurantResource;
use App\Models\Dish;
use App\Models\Restaurant;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    use HttpResponses;
    public function dishesList(Request $request) {
        $dishes = Dish::where('restaurant_id', $request->id)->get();

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

    public function updateRestaurant(Request $request, string $id) {
        $restaurant = Restaurant::where('id', $id)->first();
        $restaurant->update([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'contacts' => $request->input('contacts')
        ]);

        return new RestaurantResource($restaurant);
    }

    public function removeRestaurant(string $id) {
        $restaurant = Restaurant::where('id', $id)->first();
        $restaurant->delete();

        return true;
    }

    public function addRestaurant(StoreRestaurantRequest $request) {
        $request->validated($request->all());

        $restaurant = Restaurant::create([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'contacts' => $request->input('contacts'),
        ]);

        return new RestaurantResource($restaurant);
    }
}
