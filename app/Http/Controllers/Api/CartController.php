<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function addToCart(Request $request) {
        $orders = Order::where('status', 0)->first();

        if(is_null($orders)) {
            $orders = Order::create([
                'status' => 0,
                'user_id' => Auth::id()
            ]);
        }

        $cart = Cart::where('dish_id', $request->id)->first();

        if(is_null($cart)) {
            $cart = Cart::create([
                'count' => 1,
                'dish_id' => $request->id,
                'order_id' => $orders->id
            ]);
        } else {
            $cart->update([
                'count' => $cart->count+1
            ]);
        }

        return new CartResource($cart);
    }
}
