<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDishRequest;
use App\Http\Resources\DishesResource;
use App\Models\Dish;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Exception;

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

        try {
            DB::beginTransaction();
            $dish = Dish::create([
                'name' => $request->name,
                'price' => $request->price,
                'ingredients' => $request->ingredients,
                'category_id' => $request->category_id,
                'restaurant_id' => $request->restaurant_id
            ]);
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }


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
    public function update(StoreDishRequest $request, string $id)
    {
        $request->validated($request->all());
        $dish = Dish::where('id', $id)->first();
        $data = [
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'ingredients' => $request->input('ingredients'),
            'category_id' => $request->input('category_id'),
            'restaurant_id' => $request->input('restaurant_id'),
        ];

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

    public function destroy(string $id)
    {
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
}
