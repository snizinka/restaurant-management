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
            'status' => $this->status,
            'address' => $this->address,
            'phone' => $this->phone,
            'username' => $this->username
        ],
        'relationships' => [
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name
            ]
        ]
    ];
    }
}
