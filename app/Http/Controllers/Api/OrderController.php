<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\GeneralOrderResource;
use App\Http\Resources\OrderResource;
use App\Models\GeneralOrder;
use App\Models\Order;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Exception;

class OrderController extends Controller
{
    use HttpResponses;

    public function allOrders() {
        $orders = GeneralOrder::where('status', 1)->get();

        return GeneralOrderResource::collection($orders);
    }

    public function orderDetails(string $id) {
        $order = GeneralOrder::where('id', $id)->first();

        return new GeneralOrderResource($order);
    }

    public function assignDriver(Request $request, string $id) {
        $order = Order::where('id', $id)->first();

        try {
            DB::beginTransaction();
            $order->update([
                'driver_id' => $request->input('driver'),
                'status' => 2
            ]);
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }

        return $request->input('driver');
    }

    public function removeOrder(string $id) {
        $order = Order::where('id', $id)->first();

        try {
            DB::beginTransaction();
            $order->delete();
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }

        return true;
    }

    public function placeOrder(StoreOrderRequest $request) {
        $request->validated($request->all());
        $orders = GeneralOrder::where('user_id', Auth::id())->where('status', 0)->first();

        if (!is_null($orders)) {
            try {
                DB::beginTransaction();
                $orders->update([
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'username' => $request->username,
                    'status' => 1
                ]);
                DB::commit();
            } catch(Exception $ex) {
                DB::rollBack();
                abort(500);
            }
        } else {
            return $this->error($orders, ['noorder' => 'No orders found'], 404);
        }

        return new GeneralOrderResource($orders);
    }

    public function checkOrder() {
        $orders = GeneralOrder::where('user_id', Auth::id())->where('status', '>', 0)->get();

        return GeneralOrderResource::collection($orders);
    }

    public function averageOrderCost() {
        $orders = DB::table('orders as od')
            ->leftJoin('carts as ct', 'ct.order_id', '=', 'od.id')
            ->leftJoin('dishes as ds', 'ct.dish_id', '=', 'ds.id')
            ->selectRaw('ROUND(sum(ds.price * ct.count) / count(ds.price), 2) as price, CAST(od.created_at AS date) as date')
            ->whereRaw('DATEDIFF(current_date, od.created_at) between 0 and 30 AND od.status > 0 AND ds.id IS NOT NULL')
            ->groupBy(DB::raw('CAST(od.created_at AS date)'))
            ->get();

        return $this->success(['average' => $orders]);
    }

    public function averageDriverPaid() {
        $orders = DB::table('orders as od')
            ->leftJoin('carts as ct', 'ct.order_id', '=', 'od.id')
            ->leftJoin('drivers as ds', 'od.driver_id', '=', 'ds.id')
            ->selectRaw('sum(15) as "paid", count(*) as "deliveries", CAST(od.created_at AS date) as "date"')
            ->whereRaw('DATEDIFF(current_date, od.created_at) between 0 and 30 AND od.status > 1 AND ct.order_id = od.id')
            ->groupBy(DB::raw('CAST(od.created_at AS date)'))
            ->get();

        if ($orders->avg('paid') == null) {
            return $this->success(['average' => 0]);
        }
        $result = $orders->avg('paid') / $orders->count();
        $result = round($result, 2);

        return $this->success(['average' => $result]);
    }
}

