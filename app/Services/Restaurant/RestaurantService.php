<?php

namespace App\Services\Restaurant;

use App\Http\Resources\DishesResource;
use App\Http\Resources\RestaurantResource;
use App\Models\Dish;
use App\Models\Restaurant;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Exception;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RestaurantService
{
    private $restaurant;

    public function __construct(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
    }

    public function create($data): Restaurant {
        $restaurant = Restaurant::create([
            'name' => $data->input('name'),
            'address' => $data->input('address'),
            'contacts' => $data->input('contacts'),
        ]);

        return $restaurant;
    }

    public function update($data, $id) {
        $restaurant = Restaurant::where('id', $id)->first();

        if (is_null($restaurant)) {
            return response(
                ["id" => $id, "error" => "Couldn't find the restaurant"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }

        try {
            DB::beginTransaction();
            $restaurant->update([
                'name' => $data->input('name'),
                'address' => $data->input('address'),
                'contacts' => $data->input('contacts')
            ]);
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }

        return new RestaurantResource($restaurant);
    }

    public function delete($id): Response {
        $restaurant = Restaurant::where('id', $id)->first();

        if (is_null($restaurant)) {
            return response(
                ["id" => $id, "deleted" => false, "error" => "Couldn't delete the restaurant"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
        $restaurant->delete();

        return response(["id" => $id, "deleted" => true], ResponseAlias::HTTP_OK);
    }
}
