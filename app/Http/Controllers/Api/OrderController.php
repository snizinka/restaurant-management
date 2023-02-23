<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    use HttpResponses;
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
}
