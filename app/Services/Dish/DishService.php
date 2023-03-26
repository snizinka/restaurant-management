<?php

namespace App\Services\Dish;

use App\Http\Resources\DishesResource;
use App\Models\Dish;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Exception;

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
            return response()->json(['message' => 'Dish not found.'], 404);
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

    public function delete($id) {
        $dish = Dish::where('id', $id)->first();

        if (is_null($dish)) {
            return response()->json(['message' => 'Dish not found.'], 404);
        }

        try {
            DB::beginTransaction();
            $dish->delete();
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }

        return response()->json([], 204);
    }

    public function dishesFromRestaurant($id) {
        $dishes = Dish::where('restaurant_id', $id)->get();
        return $dishes;
    }

    public function getDish($id) {
        $dish = Dish::where('id', $id)->first();

        if(is_null($dish)) {
            return response()->json(['message' => 'Dish not found.'], 404);
        }

        return new DishesResource(
            $dish
        );
    }
}
