<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if(is_null($this->driver))
        {
            return [
                'id' => (string)$this->id,
                'attributes' => [
                    'status' => $this->status,
                    'address' => $this->address,
                    'phone' => $this->phone,
                    'username' => $this->username,
                    'delivery-status' => 'A driver is not assigned'
                ],
                'relationships' => [
                    'user' => [
                        'id' => $this->user->id,
                        'name' => $this->user->name
                    ]
                ]
            ];
        } else {
            if ($this->status == 2) {
                return [
                    'id' => (string)$this->id,
                    'attributes' => [
                        'id' => $this->driver->id,
                        'name' => $this->driver->name,
                        'delivery-status' => 'Is being delivered'
                    ]
                ];
            }else {
                return [
                    'id' => (string)$this->id,
                    'attributes' => [
                        'id' => $this->driver->id,
                        'name' => $this->driver->name,
                        'delivery-status' => 'Delivered'
                    ]
                ];
            }

        }

    }
}
