<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use App\Http\Requests\StoreDriverRequest;

class DriverController extends Controller
{
    use HttpResponses;
    public function addDriver(StoreDriverRequest $request) {
        $request->validated($request->all());

        $driver = Driver::create([
            'lastname' => $request->input('lastname'),
            'name' => $request->input('name'),
        ]);

        return new DriverResource($driver);
    }

    public function getAllDrivers() {
        $drivers = Driver::all();

        return DriverResource::collection($drivers);
    }

    public function getDriver(string $id) {
        $drivers = Driver::where('id', $id)->first();

        return new DriverResource($drivers);
    }

    public function updateDriver(StoreDriverRequest $request, string $id) {
        $drivers = Driver::where('id', $id)->first();

        if($drivers == null) {
            return [];
        }

        $drivers->update([
            'lastname' => $request->input('lastname'),
            'name' => $request->input('name'),
        ]);

        return new DriverResource($drivers);
    }

    public function removeDriver(string $id) {
        $drivers = Driver::where('id', $id)->first();

        if($drivers == null) {
            return [];
        }
        $drivers->delete();

        return $this->success('Success');
    }
}
