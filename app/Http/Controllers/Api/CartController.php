<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
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
                $carts = Cart::create([
                    'order_id' => $orders->id,
                ]);
                DB::commit();
            } catch(Exception $ex) {
                DB::rollBack();
                abort(500);
            }
        }

        $order_item = OrderItem::where('dish_id', $request->id)->where('order_id', $orders->id)->first();

        if(is_null($order_item)) {
            try {
                DB::beginTransaction();
                $order_item = OrderItem::create([
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
                $order_item->update([
                    'count' => $order_item->count+1
                ]);
                DB::commit();
            } catch(Exception $ex) {
                DB::rollBack();
                abort(500);
            }
        }

        return new CartResource($order_item);
    }

    public function removeFromCart(Request $request) {
        $orders = Order::where('user_id', Auth::id())->where('status', 0)->first();

        if(!is_null($orders)) {
            $order_item = OrderItem::where('id', $request->id)->first();
            if (!is_null($order_item)){
                if($order_item->count > 1) {
                    try {
                        DB::beginTransaction();
                        $order_item->update([
                            'count' => $order_item->count - 1
                        ]);
                        DB::commit();
                    } catch(Exception $ex) {
                        DB::rollBack();
                        abort(500);
                    }
                } else {
                    try {
                        DB::beginTransaction();
                        $order_item->delete();
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

        return new CartResource($order_item);
    }

    public function getCart() {
        $orders = Order::where('user_id', Auth::id())->where('status', 0)->first();

        if(is_null($orders)) {
            return [];
        }

        $cart = Cart::where('order_id', $orders->id)->get();
        return CartResource::collection($cart);
    }

    public function deleteCart() {
        $orders = Order::where('user_id', Auth::id())->where('status', 0)->first();

        if(is_null($orders)) {
            return [];
        }

        $cart = Cart::where('order_id', $orders->id)->get();
        $cart->delete();
        return true;
    }
}
