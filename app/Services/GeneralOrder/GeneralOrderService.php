<?php

namespace App\Services\GeneralOrder;

use App\Http\Resources\GeneralOrderResource;
use App\Models\Driver;
use App\Models\GeneralOrder;
use App\Services\OrderItem\OrderItemFacade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Exception;

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

    public function delete($id) {
        $generalOrder = GeneralOrder::where('id', $id)->first();
        if (is_null($generalOrder)) {
            return response()->json(['message' => 'General order not found.'], 404);
        }
        try {
            DB::beginTransaction();
            $generalOrder->delete();
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }
        return response()->json([], 204);
    }

    public function getDeneralOrder($id) {
        $generalOrder = GeneralOrder::where('id', $id)->first();
        if (is_null($generalOrder)) {
            return response()->json(['message' => 'General order not found.'], 404);
        }

        return new GeneralOrderResource($generalOrder);
    }

    public function assignDriverToOrder($generalOrder_id, $driver_id) {
        $generalOrder = GeneralOrder::where('id', $generalOrder_id)->first();
        $driver = Driver::where('id', $driver_id)->get();

        if (is_null($generalOrder)) {
            return response()->json(['message' => 'General order not found.'], 404);
        }

        if (is_null($driver)) {
            return response()->json(['message' => 'Driver not found.'], 404);
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
            return response()->json(['error' => 'Some dishes from the order have changed'], 500);
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
