<?php

namespace App\Services\OrderItem;

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

    public function create($dish_id, $order_id, $availability = "available"): OrderItem {
        $orderItem = OrderItem::create([
            'count' => 1,
            'dish_id' => $dish_id,
            'order_id' => $order_id,
            'availability' => $availability
        ]);

        return $orderItem;
    }

    public function delete($orderItem_id): Response {
        $order_item = OrderItem::where('id', $orderItem_id)->first();
        if (!is_null($order_item)) {
            $order_item->delete();
        } else {
            return response(
                ["id" => $orderItem_id, "deleted" => false, "error" => "Couldn't delete the order item"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }

        return response(["id" => $orderItem_id, "deleted" => true], ResponseAlias::HTTP_OK);
    }

    public function increaseOrderCount($orderItem_id): OrderItem {
        $order_item = OrderItem::where('id', $orderItem_id)->first();

        $order_item->update([
            'count' => $order_item->count + 1
        ]);

        return $order_item;
    }

    public function decreaseOrderCount($orderItem_id) {
        $order_item = OrderItem::where('id', $orderItem_id)->first();
        if (is_null($order_item)) {
            return response()->json(['data' => 'null']);
        }

        if (!is_null($order_item)){
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
        } else {
            return response()->json(['data' => 'null']);
        }

        return $order_item;
    }
}
