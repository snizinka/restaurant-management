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
                'general_order' => [
                    'id' => $this->generalOrder->id,
                    'status' => $this->generalOrder->status
                ],
                'order_items' => OrderResource::collection($this->generalOrder->orders)
            ]
        ];
    }
}
