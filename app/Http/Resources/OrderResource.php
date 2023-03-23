<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
            return [
                'id' => (string)$this->id,
                'attributes' => [
                    'order_number' => $this->order_number,
                    'restaurant' => $this->restaurant->name,
                ],
                'relationships' => [
                    'items' => OrderItemResource::collection( $this->orderItems)
                ]
            ];

    }
}
