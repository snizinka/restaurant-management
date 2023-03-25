<?php

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    private function nextUnique($restaurant_id, $previous_unique) {
        if(is_null($previous_unique)) {
            return (integer)($restaurant_id.'1');
        }

        $position = strpos((string)$previous_unique, (string)$restaurant_id);
        $result = substr($previous_unique, $position + strlen($restaurant_id));
        $newUnique = (integer)$result + 1;

        return (integer)$restaurant_id.$newUnique;
    }

    public function create($generalOrder_id, $dish):Order {
        $previous = DB::table('orders as o')
            ->join('order_items as oi', 'oi.order_id', '=', 'o.id')
            ->join('dishes as d', function ($join) use ($dish) {
                $join->on('d.id', '=', 'oi.dish_id')
                    ->where('d.restaurant_id', '=', $dish->restaurant_id);
            })
            //->where('o.general_orders_id', '=', $generalOrder->id)
            ->select('o.order_number')->orderBy('order_number', 'desc')
            ->first();

        $order = Order::create([
            'general_orders_id' => $generalOrder_id,
            'restaurant_id' => $dish->restaurant_id,
            'order_number' => $this->nextUnique($dish->restaurant_id, $previous == null ? null : $previous->order_number)
        ]);

        return $order;
    }
}
