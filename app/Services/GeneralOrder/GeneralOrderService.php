<?php

namespace App\Services\GeneralOrder;

use App\Models\GeneralOrder;
use Illuminate\Support\Facades\Auth;

class GeneralOrderService
{
    private $general_order;

    public function __construct(GeneralOrder $general_order)
    {
        $this->general_order = $general_order;
    }

    public function create(): GeneralOrder {
        $generalOrder = GeneralOrder::create([
            'status' => 0,
            'user_id' => Auth::id()
        ]);

        return $generalOrder;
    }
}
