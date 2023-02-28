<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDishRequest;
use App\Http\Resources\DishesResource;
use App\Models\Dish;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DishesController extends Controller
{
    public function index()
    {
        return DishesResource::collection(
            Dish::all()
        );
    }

    public function store(StoreDishRequest $request)
    {
        $request->validated($request->all());

        $dish = Dish::create([
            'name' => $request->name,
            'price' => $request->price,
            'ingredients' => $request->ingredients,
            'category_id' => $request->category_id,
            'restaurant_id' => $request->restaurant_id
        ]);

        return new DishesResource($dish);
    }
    public function show(string $id)
    {
        $dish = Dish::where('id', $id)->first();

        if($dish == null) {
            return [];
        }

        return new DishesResource(
            $dish
        );
    }
    public function edit(string $id)
    {
        //
    }
    public function update(Request $request, string $id)
    {
        $dish = Dish::where('id', $id)->first();
        $data = [
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'ingredients' => $request->input('ingredients'),
            'category_id' => $request->input('category_id'),
            'restaurant_id' => $request->input('restaurant_id'),
        ];

       $dish->update($data);

        return $data;
    }

    public function destroy(string $id)
    {
        $dish = Dish::where('id', $id)->first();
        $dish->delete();

        return true;
    }
}
