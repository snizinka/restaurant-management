<?php

namespace App\Services\Cart;

use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\GeneralOrder;
use Illuminate\Support\Facades\Auth;

class CartService
{
    private $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function create($generalOrder_id): Cart {
        $cart = Cart::create([
            'general_order_id' => $generalOrder_id,
        ]);

        return $cart;
    }

    public function getCart() {
        $generalOrder = GeneralOrder::where('user_id', Auth::id())->where('status', 0)->first();

        if(is_null($generalOrder)) {
            return [];
        }

        return Cart::where('general_order_id', $generalOrder->id)->get();
    }
}
