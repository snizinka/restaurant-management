<?php

namespace App\Services\GeneralOrder;

use App\Models\GeneralOrder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Exception;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class GeneralOrderService
{
    private $general_order;

    public function __construct(GeneralOrder $general_order)
    {
        $this->general_order = $general_order;
    }

    public function create(): GeneralOrder {
        $generalOrder = GeneralOrder::create([
            'status' => 0,
            'user_id' => Auth::id()
        ]);

        return $generalOrder;
    }

    public function delete($id): Response {
        $generalOrder = GeneralOrder::where('id', $id)->first();
        if (is_null($generalOrder)) {
            return response(
                ["id" => $id, "deleted" => false, "error" => "Couldn't delete the order"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
        try {
            DB::beginTransaction();
            $generalOrder->delete();
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }
        return response(["id" => $id, "deleted" => true], ResponseAlias::HTTP_OK);
    }

    public function getDeneralOrder($id): GeneralOrder {
        $generalOrder = GeneralOrder::where('id', $id)->first();

        return $generalOrder;
    }

    public function assignDriverToOrder($generalOrder_id, $driver_id): GeneralOrder {
        $generalOrder = GeneralOrder::where('id', $generalOrder_id)->first();

        try {
            DB::beginTransaction();
            $generalOrder->update([
                'driver_id' => $driver_id,
                'status' => 2
            ]);
            DB::commit();
        } catch(\Exception $ex) {
            DB::rollBack();
            abort(500);
        }

        return $generalOrder;
    }

    public function placeOrder(GeneralOrder $generalOrder, $data): GeneralOrder {
        $generalOrder->update([
            'address' => $data->address,
            'phone' => $data->phone,
            'username' => $data->username,
            'status' => 1
        ]);

        return $generalOrder;
    }
}
