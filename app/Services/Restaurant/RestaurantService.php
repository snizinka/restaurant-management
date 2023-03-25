<?php

namespace App\Services\Restaurant;

use App\Http\Resources\DishesResource;
use App\Models\Dish;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Exception;

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

    public function update($data, $id): Restaurant {
        $restaurant = Restaurant::where('id', $id)->first();

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

        return $restaurant;
    }

    public function delete($id) {
        $restaurant = Restaurant::where('id', $id)->first();
        $restaurant->delete();

        return true;
    }
}
