<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use App\Traits\HttpResponses;
use App\Http\Requests\StoreDriverRequest;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Exception;

class DriverController extends Controller
{
    use HttpResponses;
    public function addDriver(StoreDriverRequest $request) {
        $request->validated($request->all());

        try {
            DB::beginTransaction();
            $driver = Driver::create([
                'lastname' => $request->input('lastname'),
                'name' => $request->input('name'),
            ]);
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }

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
        $request->validated($request->all());
        $drivers = Driver::where('id', $id)->first();

        if($drivers == null) {
            return [];
        }

        try {
            DB::beginTransaction();
            $drivers->update([
                'lastname' => $request->input('lastname'),
                'name' => $request->input('name'),
            ]);
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }

        return new DriverResource($drivers);
    }

    public function removeDriver(string $id) {
        $drivers = Driver::where('id', $id)->first();

        if($drivers == null) {
            return [];
        }

        try {
            DB::beginTransaction();
            $drivers->delete();
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }

        return $this->success('Success');
    }
}
