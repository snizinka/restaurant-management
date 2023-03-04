<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Dish;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Exception;

class CartController extends Controller
{
    public function addToCart(Request $request) {
        $orders = Order::where('user_id', Auth::id())->where('status', 0)->first();

        if(is_null($orders)) {
            try {
                DB::beginTransaction();
                $orders = Order::create([
                    'status' => 0,
                    'user_id' => Auth::id()
                ]);
                DB::commit();
            } catch(Exception $ex) {
                DB::rollBack();
                abort(500);
            }
        }

        $cart = Cart::where('dish_id', $request->id)->where('order_id', $orders->id)->first();

        if(is_null($cart)) {
            try {
                DB::beginTransaction();
                $cart = Cart::create([
                    'count' => 1,
                    'dish_id' => $request->id,
                    'order_id' => $orders->id
                ]);
                DB::commit();
            } catch(Exception $ex) {
                DB::rollBack();
                abort(500);
            }
        } else {
            try {
                DB::beginTransaction();
                $cart->update([
                    'count' => $cart->count+1
                ]);
                DB::commit();
            } catch(Exception $ex) {
                DB::rollBack();
                abort(500);
            }
        }

        return new CartResource($cart);
    }

    public function removeFromCart(Request $request) {
        $orders = Order::where('user_id', Auth::id())->where('status', 0)->first();

        if(!is_null($orders)) {
            $cart = Cart::where('id', $request->id)->first();
            if (!is_null($cart)){
                if($cart->count > 1) {
                    try {
                        DB::beginTransaction();
                        $cart->update([
                            'count' => $cart->count - 1
                        ]);
                        DB::commit();
                    } catch(Exception $ex) {
                        DB::rollBack();
                        abort(500);
                    }
                } else {
                    try {
                        DB::beginTransaction();
                        $cart->delete();
                        DB::commit();
                    } catch(Exception $ex) {
                        DB::rollBack();
                        abort(500);
                    }
                }
            } else {
                return response()->json(['data' => 'null']);
            }
        }

        return new CartResource($cart);
    }

    public function getCart() {
        $orders = Order::where('user_id', Auth::id())->where('status', 0)->first();

        if(is_null($orders)) {
            return [];
        }

        $cart = Cart::where('order_id', $orders->id)->get();
        return CartResource::collection($cart);
    }
}
