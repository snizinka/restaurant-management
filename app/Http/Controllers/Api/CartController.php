<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Http\Resources\OrderItemResource;
use App\Models\Cart;
use App\Models\Dish;
use App\Models\GeneralOrder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\User;
use App\Services\Cart\CartFacade;
use App\Services\GeneralOrder\GeneralOrderFacade;
use App\Services\Order\OrderFacade;
use App\Services\Order\OrderService;
use App\Services\OrderItem\OrderItemFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Exception;

class CartController extends Controller
{
    public function addToCart(Request $request) {
        $dish = Dish::where('id', $request->id)->first();
        $generalOrder = GeneralOrder::where('user_id', Auth::id())->where('status', 0)->first();

        if(is_null($generalOrder)) {
            try {
                DB::beginTransaction();
                $generalOrder = GeneralOrderFacade::create();
                CartFacade::create($generalOrder->id);
                DB::commit();
            } catch(Exception $ex) {
                DB::rollBack();
                abort(500);
            }
        }
        $orders = Order::where('general_orders_id', $generalOrder->id)->
        where('restaurant_id', $dish->restaurant_id)->first();

        if (is_null($orders)) {
            $orders = OrderFacade::create($generalOrder->id, $dish);
        }

        $order_item = OrderItem::where('dish_id', $request->id)->where('order_id', $orders->id)->first();

        if(is_null($order_item)) {
            try {
                DB::beginTransaction();
                $order_item = OrderItemFacade::create($request->id, $orders->id);
                DB::commit();
            } catch(Exception $ex) {
                DB::rollBack();
                abort(500);
            }
        } else {
            try {
                DB::beginTransaction();
                OrderItemFacade::increaseOrderCount($order_item->id);
                DB::commit();
            } catch(Exception $ex) {
                DB::rollBack();
                abort(500);
            }
        }

        return new OrderItemResource($order_item);
    }

    public function removeFromCart(Request $request) {
        $generalOrder = GeneralOrder::where('user_id', Auth::id())->where('status', 0)->first();

        if(!is_null($generalOrder)) {
            $order_item = OrderItemFacade::decreaseOrderCount($request->id);
        }

        return new OrderItemResource($order_item);
    }

    public function getCart() {
        $cart = CartFacade::getCart();
        return CartResource::collection($cart);
    }

    public function deleteCart() {
        $orders = GeneralOrder::where('user_id', Auth::id())->first();

        if(is_null($orders)) {
            return [];
        }

        $cart = Cart::where('order_id', $orders->id)->get();
        $cart->delete();
        return true;
    }
}
