<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Resources\DishesResource;
use App\Http\Resources\RestaurantResource;
use App\Models\Dish;
use App\Models\Restaurant;
use App\Services\Dish\DishFacade;
use App\Services\Restaurant\RestaurantFacade;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Exception;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RestaurantController extends Controller
{
    use HttpResponses;
    public function dishesList(Request $request) {
        $dishes = DishFacade::dishesFromRestaurant($request->id);

        return DishesResource::collection($dishes);
    }

    public function restaurantList() {
        $restaurants = Restaurant::all();

        return RestaurantResource::collection($restaurants);
    }

    public function getRestaurant(string $id) {
        $restaurant = Restaurant::where('id', $id)->first();

        if($restaurant == null) {
            return response(
                ["id" => $id, "error" => "Couldn't find the restaurant"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }

        return new RestaurantResource($restaurant);
    }

    public function updateRestaurant(StoreRestaurantRequest $request, string $id) {
        $request->validated($request->all());
        $restaurant = RestaurantFacade::update($request, $id);

        return $restaurant;
    }

    public function removeRestaurant(string $id) {
        $restaurant = RestaurantFacade::delete($id);

        return $restaurant;
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
