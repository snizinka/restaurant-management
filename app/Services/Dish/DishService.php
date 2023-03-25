<?php

namespace App\Services\Dish;

use App\Http\Resources\DishesResource;
use App\Models\Dish;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Exception;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class DishService
{
    private $dish;

    public function __construct(Dish $dish)
    {
        $this->dish = $dish;
    }

    public function create($data): Dish {
        $dish = Dish::create([
            'name' => $data->name,
            'price' => $data->price,
            'ingredients' => $data->ingredients,
            'category_id' => $data->category_id,
            'restaurant_id' => $data->restaurant_id
        ]);

        return $dish;
    }

    public function update($id, $request) {
        $data = [
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'ingredients' => $request->input('ingredients'),
            'category_id' => $request->input('category_id'),
            'restaurant_id' => $request->input('restaurant_id'),
        ];

        $dish = Dish::where('id', $id)->first();
        if (is_null($dish)) {
            return response(
                ["id" => $id, "deleted" => false, "error" => "Couldn't find the dish"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }

        try {
            DB::beginTransaction();
            $dish->update($data);
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }

        return new DishesResource($dish);
    }

    public function delete($id): Response {
        $dish = Dish::where('id', $id)->first();

        if (is_null($dish)) {
            return response(
                ["id" => $id, "deleted" => false, "error" => "Couldn't delete the dish"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }

        try {
            DB::beginTransaction();
            $dish->delete();
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }

        return response(["id" => $id, "deleted" => true], ResponseAlias::HTTP_OK);
    }

    public function dishesFromRestaurant($id) {
        $dishes = Dish::where('restaurant_id', $id)->get();
        return $dishes;
    }

    public function getDish($id) {
        $dish = Dish::where('id', $id)->first();

        if(is_null($dish)) {
            return response(
                ["id" => $id, "deleted" => false, "error" => "Couldn't find the dish"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }

        return new DishesResource(
            $dish
        );
    }
}
