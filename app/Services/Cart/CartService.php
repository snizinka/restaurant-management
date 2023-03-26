<?php

namespace App\Services\Cart;

use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\GeneralOrder;
use App\Services\OrderItem\OrderItemFacade;
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

        $isAvailabilityChanged = OrderItemFacade::checkAvailability($generalOrder->id);

        if ($isAvailabilityChanged) {
            return response()->json(['error' => 'Some dishes from the order have changed'], 500);
        }

        if(is_null($generalOrder)) {
            return response()->json(['message' => 'General order not found.'], 404);
        }
        $cart = Cart::where('general_order_id', $generalOrder->id)->get();

        return new CartResource($cart[0]);
    }

    public function clearCart() {
        $orders = GeneralOrder::where('user_id', Auth::id())->where('status', '>', 0)->get();

        foreach ($orders as $order) {
            $cart = Cart::where('general_order_id', $order->id)->first();
            if (!is_null($cart)) {
                $cart->delete();
            }
        }
        return response()->json([], 204);
    }
}
