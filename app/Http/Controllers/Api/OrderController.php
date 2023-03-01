<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use HttpResponses;

    public function allOrders() {
        $orders = Order::where('status', 1)->get();

        return OrderResource::collection($orders);
    }

    public function orderDetails(string $id) {
        $order = Order::where('id', $id)->first();

        return new OrderResource($order);
    }

    public function assignDriver(Request $request, string $id) {
        $order = Order::where('id', $id)->first();
        $order->update([
            'driver_id' => $request->input('driver'),
            'status' => 2
        ]);

        return $request->input('driver');
    }

    public function removeOrder(string $id) {
        $order = Order::where('id', $id)->first();
        $order->delete();

        return true;
    }

    public function placeOrder(StoreOrderRequest $request) {
        $request->validated($request->all());
        $orders = Order::where('user_id', Auth::id())->where('status', 0)->first();

        if (!is_null($orders)) {
            $orders->update([
                'address' => $request->address,
                'phone' => $request->phone,
                'username' => $request->username,
                'status' => 1
            ]);
        } else {
            return $this->error($orders, ['noorder' => 'No orders found'], 404);
        }

        return new OrderResource($orders);
    }

    public function checkOrder() {
        $orders = Order::where('user_id', Auth::id())->where('status', '>', 0)->get();

        return OrderResource::collection($orders);
    }

    public function averageOrderCost() {
        $orders = DB::table('orders as od')
            ->leftJoin('carts as ct', 'ct.order_id', '=', 'od.id')
            ->leftJoin('dishes as ds', 'ct.dish_id', '=', 'ds.id')
            ->selectRaw('ROUND(sum(ds.price * ct.count) / count(ds.price), 2) as price, CAST(od.created_at AS date) as date')
            ->whereRaw('DATEDIFF(current_date, od.created_at) between 0 and 30 AND od.status > 0')
            ->groupBy(DB::raw('CAST(od.created_at AS date)'))
            ->get();

        return $this->success(['average' => $orders]);
    }

    public function averageDriverPaid() {
        $orders = DB::table('orders as od')
            ->leftJoin('carts as ct', 'ct.order_id', '=', 'od.id')
            ->leftJoin('drivers as ds', 'od.driver_id', '=', 'ds.id')
            ->selectRaw('sum(15) as "paid", count(*) as "deliveries", CAST(od.created_at AS date) as "date"')
            ->whereRaw('DATEDIFF(current_date, od.created_at) between 0 and 30 AND od.status > 1')
            ->groupBy(DB::raw('CAST(od.created_at AS date)'))
            ->get();

        $result = $orders->avg('paid') / $orders->count();
        $result = round($result, 2);

        return $this->success(['average' => $result]);
    }
}

