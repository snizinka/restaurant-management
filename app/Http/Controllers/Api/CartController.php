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
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CartController extends Controller
{
    public function addToCart(Request $request) {
        $dish = Dish::where('id', $request->id)->first();

        if (is_null($dish)) {
            return response(
                ["id" => $request->id, "error" => "Couldn't find the dish"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
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
            $orders = OrderFacade::create($generalOrder->id, $dish->restaurant_id);
        }

        $order_item = OrderItem::where('dish_id', $request->id)->where('order_id', $orders->id)->first();

        if(is_null($order_item)) {
            try {
                DB::beginTransaction();
                $order_item = OrderItemFacade::create($request->id, $orders->id);
                if(($order_item instanceof OrderItem) == 0) {
                    return $order_item;
                }
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
        }else {
            return response(
                ["error" => "Couldn't find the order"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }

        return $order_item;
    }

    public function getCart() {
        return CartFacade::getCart();
    }
}
