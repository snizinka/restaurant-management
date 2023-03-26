<?php

namespace App\Services\OrderItem;

use App\Http\Resources\OrderItemResource;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OrderItemService
{
    private $orderItem;

    public function __construct(OrderItem $orderItem)
    {
        $this->orderItem = $orderItem;
    }

    public function create($dish_id, $order_id, $availability = "available") {
        $dish = Dish::withTrashed()->where('id', $dish_id)->first();
        if (is_null($dish)) {
            return response()->json(['message' => 'Order item not found.'], 404);
        }

        if ($availability == "available") {
            if (!is_null($dish->deleted_at)) {
                $availability = "deleted";
            } else if($dish->availability != 1) {
                $availability = "unavailable";
            }
        }

        $orderItem = OrderItem::create([
            'count' => 1,
            'dish_id' => $dish_id,
            'order_id' => $order_id,
            'availability' => $availability
        ]);

        return $orderItem;
    }

    public function delete($orderItem_id) {
        $order_item = OrderItem::where('id', $orderItem_id)->first();
        if (!is_null($order_item)) {
            $order_item->delete();
        } else {
            return response()->json(['message' => 'Order item not found.'], 404);
        }

        return response()->json([], 204);
    }

    public function increaseOrderCount($orderItem_id) {
        $order_item = OrderItem::where('id', $orderItem_id)->first();

        if (is_null($this->orderItem)) {
            return response()->json(['message' => 'Order item not found.'], 404);
        }

        $order_item->update([
            'count' => $order_item->count + 1
        ]);

        return $order_item;
    }

    public function decreaseOrderCount($orderItem_id) {
        $order_item = OrderItem::where('id', $orderItem_id)->first();
        if (is_null($order_item)) {
            return response()->json(['message' => 'Order item not found.'], 404);
        }
        else{
            if($order_item->count > 1) {
                try {
                    DB::beginTransaction();
                    $order_item->update([
                        'count' => $order_item->count - 1
                    ]);
                    DB::commit();
                } catch(\Exception $ex) {
                    DB::rollBack();
                    abort(500);
                }
            } else {
                try {
                    DB::beginTransaction();
                    OrderItemFacade::delete($order_item->id);
                    DB::commit();
                } catch(\Exception $ex) {
                    DB::rollBack();
                    abort(500);
                }
            }
        }

        return new OrderItemResource($order_item);
    }

    public function checkAvailability($generalOrder_id) {
        $orders = Order::where('general_orders_id', $generalOrder_id)->get();
        $isAvailabilityChanged = false;

        foreach ($orders as $order) {
            $orderItems = OrderItem::where('order_id', $order->id)->get();
            foreach ($orderItems as $orderItem) {

                    $dish = Dish::withTrashed()->where('id', $orderItem->dish_id)->first();
                    $availability = 'available';
                    if (!is_null($dish->deleted_at)) {
                        $availability = 'deleted';
                    } else if ($dish->availability != 1) {
                        $availability = 'unavailable';
                    } else if ($dish->updated_at > $orderItem->created_at) {
                        $availability = 'edited';
                    }

                    if ($availability != "available") {
                        if($orderItem->availability == "available") {
                        $isAvailabilityChanged = true;
                        $orderItem->update([
                            'availability' => $availability
                        ]);
                        }
                    }else {
                        if($orderItem->availability != "available") {
                        $isAvailabilityChanged = true;
                        $orderItem->update([
                            'availability' => $availability
                        ]);
                            }
                    }
            }
        }

        return $isAvailabilityChanged;
    }
}
