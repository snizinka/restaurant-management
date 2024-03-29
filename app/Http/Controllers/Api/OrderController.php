<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\GeneralOrderResource;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Dish;
use App\Models\GeneralOrder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Cart\CartFacade;
use App\Services\GeneralOrder\GeneralOrderFacade;
use App\Services\Order\OrderFacade;
use App\Services\OrderItem\OrderItemFacade;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\Exception;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OrderController extends Controller
{
    use HttpResponses;

    public function allOrders() {
        $generalOrder = GeneralOrder::where('status', 1)->get();

        return GeneralOrderResource::collection($generalOrder);
    }

    public function orderDetails(string $id) {
        $generalOrder = GeneralOrderFacade::getDeneralOrder($id);

        return $generalOrder;
    }

    public function assignDriver(Request $request, string $id) {
        $generalOrder = GeneralOrderFacade::assignDriverToOrder($id, $request->input('driver'));

        return $generalOrder;
    }

    public function removeOrder(string $id) {

        return GeneralOrderFacade::delete($id);
    }

    public function placeOrder(StoreOrderRequest $request) {
        $request->validated($request->all());
        $generalOrder = GeneralOrder::where('user_id', Auth::id())->where('status', 0)->first();

        if (!is_null($generalOrder)) {
            try {
                DB::beginTransaction();
                $generalOrder = GeneralOrderFacade::placeOrder($generalOrder, $request);
                if ($generalOrder instanceof GeneralOrderResource) {
                    CartFacade::clearCart();
                }
                DB::commit();
            } catch(Exception $ex) {
                DB::rollBack();
                abort(500);
            }
        } else {
            return response(
                ["error" => "Couldn't find the order"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }

        return $generalOrder;
    }

    public function checkOrder() {
        $orders = GeneralOrder::where('user_id', Auth::id())->where('status', '>', 0)->get();

        return GeneralOrderResource::collection($orders);
    }

    public function averageOrderCost() {
        $orders = DB::table('general_orders as go')
            ->join('orders as od', 'od.general_orders_id', '=', 'go.id')
            ->join('order_items as ct', 'ct.order_id', '=', 'od.id')
            ->join('dishes as ds', 'ct.dish_id', '=', 'ds.id')
            ->selectRaw('ROUND(sum(ds.price * ct.count) / count(ds.price), 2) as price, DATE(go.created_at) as date')
            ->whereRaw('DATEDIFF(current_date, od.created_at) between 0 and 30 AND go.status > 0 AND ds.id IS NOT NULL')
            ->groupBy(DB::raw('DATE(go.created_at)'))
            ->get();

        return $this->success(['average' => $orders]);
    }

    public function averageDriverPaid() {
        $orders = DB::table('general_orders as go')
            ->join('orders as od', 'od.general_orders_id', '=', 'go.id')
            ->join('order_items as ct', 'ct.order_id', '=', 'od.id')
            ->join('drivers as ds', 'go.driver_id', '=', 'ds.id')
            ->selectRaw('SUM(15) as paid, COUNT(*) as deliveries, DATE(go.created_at) as date')
            ->whereRaw('DATEDIFF(current_date, od.created_at) between 0 and 30 AND go.status > 1 AND ct.order_id = od.id')
            ->groupBy(DB::raw('DATE(go.created_at)'))
            ->get();

        if ($orders->avg('paid') == null) {
            return $this->success(['average' => 0]);
        }
        $result = $orders->avg('paid') / $orders->count();
        $result = round($result, 2);

        return $this->success(['average' => $result]);
    }

    public function duplicateOrder(Request $request) {
        $placeOrder = true;
        $generalOrder = GeneralOrder::where('id', $request->id)->first();
        if (is_null($generalOrder)) {
            return response()->json(['message' => 'General order not found.'], 404);
        }
        else if ($generalOrder->user_id != Auth::id()) {
            return response()->json(['error' => "You are trying to access someone else's order"], 403);
        } else if($generalOrder->status == 0) {
            return response()->json(['message' => 'Completed order not found.'], 404);
        }

        $orders = Order::where('general_orders_id', $generalOrder->id)->get();
        if (is_null($orders)) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $curentGeneralOrder = GeneralOrder::where('user_id', Auth::id())->where('status', 0)->first();

        if (!is_null($curentGeneralOrder)) {
           GeneralOrderFacade::delete($curentGeneralOrder->id);
        }

        $duplicateGeneralOrder = GeneralOrderFacade::create();
        CartFacade::create($duplicateGeneralOrder->id);

        foreach ($orders as $order) {
            $duplicateOrder = OrderFacade::create($duplicateGeneralOrder->id, $order->restaurant_id);
            $orderItems = OrderItem::where('order_id', $order->id)->get();
            if (is_null($orderItems)) {
                return response()->json(['message' => 'Order item order not found.'], 404);
            }

            foreach ($orderItems as $orderItem) {
                $dish = Dish::withTrashed()->where('id', $orderItem->dish_id)->first();
                if (is_null($dish)) {
                    return response()->json(['message' => 'Dish not found.'], 404);
                }
                $availability = 'available';
                if ($dish->availability != 1) {
                    $availability = 'unavailable';
                } else if($dish->updated_at > $orderItem->updated_at) {
                    $availability = 'updated';
                } else if($orderItem->availability != "available") {
                    $availability = $orderItem->availability;
                }

                if (!is_null($dish->deleted_at)) {
                    $availability = 'deleted';
                }

                if ($availability != "available") {
                    $placeOrder = false;
                }

                OrderItemFacade::create($orderItem->dish_id, $duplicateOrder->id, $availability);
            }
        }

        if ($placeOrder) {
            $duplicateGeneralOrder->update([
                'address' => $generalOrder->address,
                'phone' => $generalOrder->phone,
                'username' => $generalOrder->username,
                'status' => is_null($generalOrder->address) ? 0 : 1
            ]);
            if (!is_null($generalOrder->address)) {
                CartFacade::clearCart();
            }
        }

        return new GeneralOrderResource($duplicateGeneralOrder);
    }
}

