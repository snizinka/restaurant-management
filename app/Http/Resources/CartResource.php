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
            'attributes' => [
                'count' => $this->count,
            ],
            'relationships' => [
                'order' => [
                    'id' => $this->order->id,
                    'status' => $this->order->status
                ],
                'dish' => [
                    'id' => $this->dish->id,
                    'name' => $this->dish->name,
                    'price' => $this->dish->price,
                    'ingredients' => $this->dish->ingredients,
                ]
            ]
        ];
    }
}
