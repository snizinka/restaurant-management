<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function getAllDrivers() {
        $drivers = Driver::all();

        return DriverResource::collection($drivers);
    }
}
