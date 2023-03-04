<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DishesResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
          'id' => (string)$this->id,
            'attributes' => [
                'name' => $this->name,
                'price' => $this->price,
                'ingredients' => $this->ingredients,
            ],
            'relationships' => [
                'category' => [
                    'id' => $this->category->id,
                    'name' => $this->category->name
                ],
                'restaurant' => [
                    'id' => $this->restaurant->id,
                    'name' => $this->restaurant->name,
                ]
            ]
        ];
    }
}
