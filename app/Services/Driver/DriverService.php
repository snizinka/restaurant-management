<?php

namespace App\Services\Driver;

use App\Http\Resources\DriverResource;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Exception;

class DriverService
{
    private $driver;

    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    public function create($data): Driver {
        $driver = Driver::create([
            'lastname' => $data->input('lastname'),
            'name' => $data->input('name'),
        ]);

        return $driver;
    }

    public function update($id, $data) {
        $drivers = Driver::where('id', $id)->first();

        if($drivers == null) {
            return response()->json(['message' => 'Driver not found.'], 404);
        }

        try {
            DB::beginTransaction();
            $drivers->update([
                'lastname' => $data->input('lastname'),
                'name' => $data->input('name'),
            ]);
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }

        return new DriverResource($drivers);
    }

    public function delete($id) {
        $drivers = Driver::where('id', $id)->first();

        if(is_null($drivers)) {
            return response()->json(['message' => 'Driver not found.'], 404);
        }

        try {
            DB::beginTransaction();
            $drivers->delete();
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }

        return response()->json([], 204);
    }

    public function getDriver($id) {
        $driver = Driver::where('id', $id)->first();

        if (is_null($driver)) {
            return response()->json(['message' => 'Driver not found.'], 404);
        }

        return new DriverResource($driver);
    }
}
