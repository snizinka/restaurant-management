<?php

namespace App\Services\Driver;

use App\Http\Resources\DriverResource;
use App\Models\Driver;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Exception;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

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
            return response(
                ["id" => $id, "error" => "Couldn't find the driver"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
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
            return response(
                ["id" => $id, "error" => "Couldn't find the driver"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }

        try {
            DB::beginTransaction();
            $drivers->delete();
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }

        return response(["id" => $id, "deleted" => true], ResponseAlias::HTTP_OK);
    }

    public function getDriver($id) {
        $driver = Driver::where('id', $id)->first();

        if (is_null($driver)) {
            return response(
                ["id" => $id, "error" => "Couldn't find the driver"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }

        return new DriverResource($driver);
    }
}
