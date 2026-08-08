<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OrderResource;
use App\Http\Traits\ApiResponse;
use App\Models\Cart;
use App\Models\Order;
use App\Models\UserAddress;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;

    public function checkout(Request $request)
    {
        $user = $request->user('user-api');

        $request->validate([
            'address_id' => 'required|exists:user_addresses,id',
            'notes'      => 'sometimes|nullable|string',
        ]);

        // Verify address belongs to user
        $address = UserAddress::where('user_id', $user->id)
            ->with('deliveryZone')
            ->findOrFail($request->address_id);

        $cart = Cart::where('user_id', $user->id)
            ->with('items.product')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return $this->error('السلة فارغة', null, 422);
        }

        $subtotal    = $cart->items->sum(fn ($item) => $item->unit_price * $item->quantity);
        $deliveryFee = $address->deliveryZone ? (float) $address->deliveryZone->fee : 0;
        $total       = $subtotal + $deliveryFee;

        $order = Order::create([
            'user_id'          => $user->id,
            'address_id'       => $address->id,
            'delivery_zone_id' => $address->delivery_zone_id,
            'subtotal'         => $subtotal,
            'delivery_fee'     => $deliveryFee,
            'total'            => $total,
            'status'           => 'pending',
            'payment_status'   => 'unpaid',
            'notes'            => $request->notes,
        ]);

        foreach ($cart->items as $item) {
            $order->items()->create([
                'product_id'   => $item->product_id,
                'product_name' => $item->product->name,
                'unit_price'   => $item->unit_price,
                'quantity'     => $item->quantity,
                'total'        => $item->unit_price * $item->quantity,
            ]);
        }

        // Clear cart
        $cart->items()->delete();

        $order->load('items', 'address.deliveryZone');

        return $this->success(new OrderResource($order), 'تم إنشاء الطلب بنجاح', 201);
    }

    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user('user-api')->id)
            ->latest()
            ->paginate(15);

        return $this->success(OrderResource::collection($orders)->response()->getData(true));
    }

    public function show(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user('user-api')->id)
            ->with('items', 'address.deliveryZone')
            ->findOrFail($id);

        return $this->success(new OrderResource($order));
    }
}
