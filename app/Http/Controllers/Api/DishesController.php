<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDishRequest;
use App\Http\Resources\DishesResource;
use App\Models\Dish;
use App\Models\User;
use App\Services\Dish\DishFacade;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function store(StoreDishRequest $request): DishesResource
    {
        $request->validated($request->all());

        try {
            DB::beginTransaction();
            $dish = DishFacade::create($request);
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }

        return new DishesResource($dish);
    }
    public function show(string $id)
    {
        return DishFacade::getDish($id);
    }
    public function edit(string $id)
    {
        //
    }
    public function update(StoreDishRequest $request, string $id): DishesResource
    {
        $request->validated($request->all());
        $dish = DishFacade::update($id, $request);


        return $dish;
    }

    public function destroy(string $id): Response
    {
        return DishFacade::delete($id);
    }
}
