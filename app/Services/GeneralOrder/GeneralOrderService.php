<?php

namespace App\Services\GeneralOrder;

use App\Http\Resources\GeneralOrderResource;
use App\Models\Dish;
use App\Models\Driver;
use App\Models\GeneralOrder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderItem\OrderItemFacade;
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

    public function getDeneralOrder($id) {
        $generalOrder = GeneralOrder::where('id', $id)->first();
        if (is_null($generalOrder)) {
            return response(
                ["id" => $id, "error" => "Couldn't find the restaurant"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }

        return new GeneralOrderResource($generalOrder);
    }

    public function assignDriverToOrder($generalOrder_id, $driver_id) {
        $generalOrder = GeneralOrder::where('id', $generalOrder_id)->first();
        $driver = Driver::where('id', $driver_id)->get();

        if (is_null($generalOrder)) {
            return response(
                ["id" => $generalOrder_id, "error" => "Couldn't find the order"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }

        if (is_null($driver)) {
            return response(
                ["id" => $generalOrder_id, "error" => "Couldn't find the driver"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }

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

        return new GeneralOrderResource($generalOrder);
    }

    public function placeOrder(GeneralOrder $generalOrder, $data) {
        $isAvailabilityChanged = OrderItemFacade::checkAvailability($generalOrder->id);

        if ($isAvailabilityChanged) {
            return response(
                ["error" => "Some dishes from the order have changed"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }

        $generalOrder->update([
            'address' => $data->address,
            'phone' => $data->phone,
            'username' => $data->username,
            'status' => 1
        ]);

        return new GeneralOrderResource($generalOrder);
    }
}
