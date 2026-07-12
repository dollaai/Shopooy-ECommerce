<?php

namespace App\Http\Controllers;

use App\Models\Cart\Cart;
use App\Models\Products\Product;
use App\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{

    private function getOrCreateCart()
    {
        $user = Auth::user();
        $cart = Cart::with(['items', 'address'])->where('user_id', $user->id)->first();

        if (is_null($cart)) {
            $cart = Cart::create([  
                'user_id' => $user->id,
                'address_id' => optional($user->addresses()->where('is_default', 1)->first())->id,
                'courier' => null,
                'courier_type' => null,
                'courier_estimation' => null,
                'courier_price' => 0,
                'voucher_id' => null,
                'voucher_value' => 0,
                'voucher_cashback' => 0,
                'service_fee' => 0,
                'total' => 0,
                'pay_with_coin' => 0,
                'payment_method' => null,
                'total_payment' => 0,
            ]);
            $cart->refresh();
        }
        $cart->recalculateTotal();
        return $cart;
    }
    public function getCart()
    {
        $cart = $this->getOrCreateCart();
        return ResponseFormatter::success([
            'cart' => $cart->api_response,
            'items' => $cart->items->pluck('api_response'),
        ]);
    }
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_uuid' => 'required|exists:products,uuid',
            'variations' => 'nullable|array',
            'variations.*.name' => 'required|exists:variations,name',
            'variations.*.value' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:255',

        ]);
        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $cart = $this->getOrCreateCart();
        $product = Product::where('uuid', $request->product_uuid)->firstOrFail();
        if ($product->stock < $request->quantity) {
            return ResponseFormatter::error(400, ['Insufficient stock for the product']);
        }

        $firstItem = $cart->items()->with('product')->first();

        if ($firstItem && $firstItem->product->seller_id !== $product->seller_id) {
            return ResponseFormatter::error(400, ['You cannot add products from different sellers to the cart']);
        }

        $cart->items()->create([
            'product_uuid' => $product->uuid,
            'variations' => $request->variations,
            'quantity' => $request->quantity,
            'note' => $request->note,
        ]);

        return $this->getCart();
    }

    public function updateCartItem(Request $request, $uuid)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:255',
            'variations' => 'nullable|array',
            'variations.*.name' => 'required|exists:variations,name',
            'variations.*.value' => 'required|string',
        ]);
        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $cart = $this->getOrCreateCart();
        $item = $cart->items()->where('product_uuid', $uuid)->firstOrFail();

        if ($item->product->stock < $request->quantity) {
            return ResponseFormatter::error(400, ['Insufficient stock for the product']);
        }

        $item->update([
            'quantity' => $request->quantity,
            'note' => $request->note,
            'variations' => $request->variations,
        ]);

        return $this->getCart();
    }

    public function removeCartItem($uuid)
    {
        $cart = $this->getOrCreateCart();
        $item = $cart->items()->where('product_uuid', $uuid)->firstOrFail();

        if (!$item) {
            return ResponseFormatter::error(404, ['Cart item not found']);
        }

        $item->delete();

        return $this->getCart();
    }
}
