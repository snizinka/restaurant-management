<?php

namespace App\Services\Dish;

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

    public function update($id, $request):Dish {
        $data = [
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'ingredients' => $request->input('ingredients'),
            'category_id' => $request->input('category_id'),
            'restaurant_id' => $request->input('restaurant_id'),
        ];

        $dish = Dish::where('id', $id)->first();
        $dish->update($data);

        return $dish;
    }

    public function delete($id) {
        $dish = Dish::where('id', $id)->first();

        try {
            DB::beginTransaction();
            $dish->delete();
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }

        return true;
    }

    public function dishesFromRestaurant($id) {
        $dishes = Dish::where('restaurant_id', $id)->get();
        return $dishes;
    }
}
