<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BathingRequestResource;
use App\Http\Traits\ApiResponse;
use App\Models\BathingCard;
use App\Models\BathingRequest;
use Illuminate\Http\Request;

class BathingController extends Controller
{
    use ApiResponse;

    public function redeem(Request $request)
    {
        $request->validate([
            'code'       => 'required|string',
            'date'       => 'required|date',
            'time'       => 'required|string',
            'address_id' => 'sometimes|nullable|exists:user_addresses,id',
            'notes'      => 'sometimes|nullable|string',
        ]);

        $card = BathingCard::where('code', $request->code)
            ->where('is_active', true)
            ->first();

        if (!$card) {
            return $this->error('البطاقة غير موجودة أو غير مفعّلة', null, 422);
        }

        if ($card->used_count >= $card->max_uses) {
            return $this->error('البطاقة مستنفذة', null, 422);
        }

        $user = $request->user('user-api');

        $bathingRequest = BathingRequest::create([
            'user_id'        => $user->id,
            'patient_code'   => $user->patient_code ?? '',
            'payment_type'   => 'card',
            'bathing_card_id'=> $card->id,
            'address_id'     => $request->address_id,
            'booking_date'   => $request->date,
            'booking_time'   => $request->time,
            'notes'          => $request->notes,
            'status'         => 'pending',
        ]);

        $card->increment('used_count');

        return $this->success(new BathingRequestResource($bathingRequest), 'تم استخدام البطاقة وإنشاء الطلب', 201);
    }

    /**
     * Create a bathing request without a card code — keyed to a saved address instead.
     */
    public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:user_addresses,id',
            'date'       => 'required|date',
            'time'       => 'required|string',
            'notes'      => 'sometimes|nullable|string',
        ]);

        $user = $request->user('user-api');

        $bathingRequest = BathingRequest::create([
            'user_id'         => $user->id,
            'patient_code'    => $user->patient_code ?? '',
            'payment_type'    => 'cash',
            'bathing_card_id' => null,
            'address_id'      => $request->address_id,
            'booking_date'    => $request->date,
            'booking_time'    => $request->time,
            'notes'           => $request->notes,
            'status'          => 'pending',
        ]);

        return $this->success(new BathingRequestResource($bathingRequest), 'تم إرسال طلب الاستحمام', 201);
    }

    public function index(Request $request)
    {
        $requests = BathingRequest::where('user_id', $request->user('user-api')->id)
            ->with('bathingCard')
            ->latest()
            ->paginate(15);

        return $this->success(BathingRequestResource::collection($requests)->response()->getData(true));
    }
}
