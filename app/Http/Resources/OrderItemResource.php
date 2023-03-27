<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'order_id' => $this->order_id,
            'dish' => [
                'id' => $this->dish->id,
                'name' => $this->dish->name,
                'price' => $this->dish->price,
                'ingredients' => $this->dish->ingredients,
                'availability' => $this->availability
            ],
            'restaurant' => [
                'id' => $this->dish->restaurant->id,
                'name' => $this->dish->restaurant->name,
            ],
            'count' => $this->count,
            'availability' => $this->availability,
        ];
    }
}
