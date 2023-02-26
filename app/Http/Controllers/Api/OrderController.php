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
            return $this->error($orders, 'No orders found', 404);
        }

        return new OrderResource($orders);
    }
// FIX ADD CART FUNCTION
    public function checkOrder() {
        $orders = Order::where('user_id', Auth::id())->where('status', '>', 0)->get();

        return OrderResource::collection($orders);
    }

    public function averageOrderCost() {
        $orders = Order::whereRaw("DATEDIFF('" . Carbon::now() . "',created_at)  between 0 and 30 ")->where('status', '>', 0)->get();

        $average = [];
        foreach ($orders as $order) {
            $cartAvg = 0;
            foreach ($order->carts as $cart) {
                $cartAvg += $cart->dish->price * $cart->count;
            }

            $average[$order->id] = $cartAvg;
            //array_push($average, $cartAvg);
        }

        return $average;
    }
}

