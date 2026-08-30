<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OrderResource;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\ResolvesPatientCode;
use App\Models\Cart;
use App\Models\Order;
use App\Models\UserAddress;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse, ResolvesPatientCode;

    public function __construct(private FirebaseService $firebase)
    {
    }

    public function checkout(Request $request)
    {
        $user = $request->user('user-api');

        $request->validate([
            'address_id'   => 'required|exists:user_addresses,id',
            'notes'        => 'sometimes|nullable|string',
            'patient_code' => 'sometimes|nullable|string|max:20',
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

        [$room, $visitForm, $codeError] = $this->resolveCodeSource($request->patient_code, $user);

        if ($codeError) {
            return $this->error($codeError, null, 403);
        }

        $discountSource = $room ?? $visitForm;

        // The discount applies to the goods (subtotal) only — delivery is
        // charged at full price regardless.
        $discountedSubtotal = $discountSource ? $discountSource->applyDiscount($subtotal) : $subtotal;
        $total = $discountedSubtotal + $deliveryFee;

        $order = Order::create([
            'user_id'          => $user->id,
            'address_id'       => $address->id,
            'delivery_zone_id' => $address->delivery_zone_id,
            'patient_code'     => $request->patient_code,
            'patient_id'       => $room?->patient_id ?? $visitForm?->patient_id,
            'room_id'          => $room?->id,
            'visit_form_id'    => $visitForm?->id,
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

        if ($room) {
            $itemsSummary = $order->items->map(fn ($i) => "{$i->product_name} × {$i->quantity}")->implode('، ');
            $this->firebase->postSystemMessage(
                $room,
                "قام {$user->name} بإنشاء طلب من المتجر رقم #{$order->id}: {$itemsSummary} — بقيمة {$total} دينار — بانتظار التأكيد"
            );
        }

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
