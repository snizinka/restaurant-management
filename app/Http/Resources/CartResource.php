<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'relationships' => [
                'order' => [
                    'id' => $this->order->id,
                    'status' => $this->order->status
                ],
                'order_items' => OrderItemResource::collection($this->order->orderItems)
            ]
        ];
    }
}
