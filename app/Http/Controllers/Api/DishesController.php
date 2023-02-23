<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDishRequest;
use App\Http\Resources\DishesResource;
use App\Models\Dish;
use Illuminate\Http\Request;

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
        //
    }
    public function edit(string $id)
    {
        //
    }
    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
