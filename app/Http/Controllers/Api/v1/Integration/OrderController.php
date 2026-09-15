<?php

namespace App\Http\Controllers\Api\v1\Integration;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OrderResource;
use App\Http\Traits\ApiResponse;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * Full-admin order management for external platforms authenticated via
 * API key (see VerifyApiKey middleware) — every user's orders, not scoped
 * to a single acting user.
 */
class OrderController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Order::with(['user', 'address', 'items'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        $orders = $query->paginate(20);

        return $this->success(OrderResource::collection($orders)->response()->getData(true));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'address.deliveryZone', 'items.product'])->findOrFail($id);

        return $this->success(new OrderResource($order));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return $this->success(new OrderResource($order->fresh(['user', 'items'])), 'تم تحديث حالة الطلب');
    }
}
