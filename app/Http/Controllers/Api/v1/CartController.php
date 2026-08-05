<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CartResource;
use App\Http\Traits\ApiResponse;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponse;

    private function getUserCart(Request $request): Cart
    {
        $user = $request->user('user-api');

        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    public function show(Request $request)
    {
        $cart = $this->getUserCart($request);
        $cart->load('items.product');

        return $this->success(new CartResource($cart));
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::where('is_active', true)->findOrFail($request->product_id);

        $cart = $this->getUserCart($request);

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $item->increment('quantity', $request->quantity);
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
                'unit_price' => $product->sale_price ?? $product->price,
            ]);
        }

        $cart->load('items.product');

        return $this->success(new CartResource($cart), 'تم إضافة المنتج للسلة');
    }

    public function updateItem(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user('user-api');
        $cart = Cart::where('user_id', $user->id)->firstOrFail();

        $item = CartItem::where('cart_id', $cart->id)->findOrFail($itemId);
        $item->update(['quantity' => $request->quantity]);

        $cart->load('items.product');

        return $this->success(new CartResource($cart), 'تم تحديث الكمية');
    }

    public function removeItem(Request $request, $itemId)
    {
        $user = $request->user('user-api');
        $cart = Cart::where('user_id', $user->id)->firstOrFail();

        CartItem::where('cart_id', $cart->id)->findOrFail($itemId)->delete();

        return $this->success(null, 'تم حذف المنتج من السلة');
    }

    public function clear(Request $request)
    {
        $cart = $this->getUserCart($request);
        $cart->items()->delete();

        return $this->success(null, 'تم تفريغ السلة');
    }
}
