<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use App\Services\Driver\DriverFacade;
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
            $driver = DriverFacade::create($request);
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

        return DriverFacade::getDriver($id);
    }

    public function updateDriver(StoreDriverRequest $request, string $id) {
        $request->validated($request->all());

        return DriverFacade::update($id, $request);
    }

    public function removeDriver(string $id) {

        return DriverFacade::delete($id);
    }
}
