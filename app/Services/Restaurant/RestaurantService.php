<?php

namespace App\Services\Restaurant;

use App\Http\Resources\RestaurantResource;
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

    public function update($data, $id) {
        $restaurant = Restaurant::where('id', $id)->first();

        if (is_null($restaurant)) {
            return response()->json(['message' => 'Restaurant not found.'], 404);
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

    public function delete($id) {
        $restaurant = Restaurant::where('id', $id)->first();

        if (is_null($restaurant)) {
            return response()->json(['message' => 'Restaurant not found.'], 404);
        }
        $restaurant->delete();

        return response()->json([], 204);
    }
}
