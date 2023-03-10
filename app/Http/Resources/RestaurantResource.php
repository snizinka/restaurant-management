<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
        'id' => (string)$this->id,
        'attributes' => [
            'name' => $this->name,
            'address' => $this->address,
            'contacts' => $this->contacts,
        ]
    ];
    }
}
