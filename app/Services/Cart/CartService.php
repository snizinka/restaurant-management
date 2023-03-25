<?php

namespace App\Services\Cart;

use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Dish;
use App\Models\GeneralOrder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderItem\OrderItemFacade;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

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

        $isAvailabilityChanged = OrderItemFacade::checkAvailability($generalOrder->id);

        if ($isAvailabilityChanged) {
            return response(
                ["error" => "Some dishes from the order have changed"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }

        if(is_null($generalOrder)) {
            return response(
                ["error" => "Couldn't find the order"],
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
        $cart = Cart::where('general_order_id', $generalOrder->id)->get();

        return new CartResource($cart[0]);
    }
}
