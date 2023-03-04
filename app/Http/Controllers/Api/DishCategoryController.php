<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DishCategoryResource;
use App\Models\DishCategory;
use Illuminate\Http\Request;

class DishCategoryController extends Controller
{
    public function categories() {
        $categories = DishCategory::all();

        return DishCategoryResource::collection($categories);
    }
}
