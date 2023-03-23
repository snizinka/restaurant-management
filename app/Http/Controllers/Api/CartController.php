<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Dish;
use App\Models\GeneralOrder;
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
    function nextUnique($restaurant_id, $previous_unique) {
        if(is_null($previous_unique)) {
            return (integer)($restaurant_id.'1');
        }

        $position = strpos((string)$previous_unique, (string)$restaurant_id);
        $result = substr($previous_unique, $position + strlen($restaurant_id));
        $newUnique = (integer)$result + 1;

        return (integer)$restaurant_id.$newUnique;
    }

    public function addToCart(Request $request) {
        $dish = Dish::where('id', $request->id)->first();
        $generalOrder = GeneralOrder::where('user_id', Auth::id())->where('status', 0)->first();

        if(is_null($generalOrder)) {
            try {
                DB::beginTransaction();
                $generalOrder = GeneralOrder::create([
                    'status' => 0,
                    'user_id' => Auth::id()
                ]);

                Cart::create([
                    'general_order_id' => $generalOrder->id,
                ]);
                DB::commit();
            } catch(Exception $ex) {
                DB::rollBack();
                abort(500);
            }
        }
        $orders = Order::where('general_orders_id', $generalOrder->id)->
        where('restaurant_id', $dish->restaurant_id)->first();

        if (is_null($orders)) {
            $orders = Order::create([
                'general_orders_id' => $generalOrder->id,
                'restaurant_id' => $dish->restaurant_id
            ]);
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
        $generalOrder = GeneralOrder::where('user_id', Auth::id())->where('status', 0)->first();

        if(!is_null($generalOrder)) {
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
