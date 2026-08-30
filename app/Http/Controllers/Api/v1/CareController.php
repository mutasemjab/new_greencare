<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CareRequestResource;
use App\Http\Resources\Api\CareServiceResource;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\ResolvesPatientCode;
use App\Models\CareRequest;
use App\Models\CareRequestService;
use App\Models\CareService;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class CareController extends Controller
{
    use ApiResponse, ResolvesPatientCode;

    public function __construct(private FirebaseService $firebase)
    {
    }

    public function services()
    {
        $services = CareService::where('is_active', true)->get();

        return $this->success(CareServiceResource::collection($services));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'          => 'required|date',
            'time'          => 'required|string',
            'address_id'    => 'sometimes|nullable|exists:user_addresses,id',
            'notes'         => 'sometimes|nullable|string',
            'service_ids'   => 'required|array|min:1',
            'service_ids.*' => 'exists:care_services,id',
            'patient_code'  => 'sometimes|nullable|string|max:20',
        ]);

        $user = $request->user('user-api');

        [$room, $visitForm, $codeError] = $this->resolveCodeSource($request->patient_code, $user);

        if ($codeError) {
            return $this->error($codeError, null, 403);
        }

        $discountSource = $room ?? $visitForm;

        $careRequest = CareRequest::create([
            'user_id'      => $user->id,
            'patient_code' => $request->patient_code ?? '',
            'address_id'   => $request->address_id,
            'booking_date' => $request->date,
            'booking_time' => $request->time,
            'notes'        => $request->notes,
            'status'       => 'pending',
        ]);

        $services = CareService::whereIn('id', $request->service_ids)->get();
        foreach ($services as $service) {
            CareRequestService::create([
                'care_request_id' => $careRequest->id,
                'care_service_id' => $service->id,
                'unit_price'      => $service->price,
            ]);
        }

        $total = $services->sum('price');
        $total = $discountSource ? $discountSource->applyDiscount($total) : $total;
        $careRequest->update(['total' => $total]);

        $careRequest->load('services.service');

        if ($room) {
            $serviceNames = $services->pluck('name')->implode('، ');
            $this->firebase->postSystemMessage(
                $room,
                "قام {$user->name} بطلب خدمة رعاية رقم #{$careRequest->id}: {$serviceNames} — بقيمة {$total} دينار — بانتظار التأكيد"
            );
        }

        return $this->success(new CareRequestResource($careRequest), 'تم إرسال طلب الرعاية', 201);
    }

    public function index(Request $request)
    {
        $requests = CareRequest::where('user_id', $request->user('user-api')->id)
            ->with('services.service')
            ->latest()
            ->paginate(15);

        return $this->success(CareRequestResource::collection($requests)->response()->getData(true));
    }

    public function show(Request $request, int $id)
    {
        $careRequest = CareRequest::where('user_id', $request->user('user-api')->id)
            ->with('services.service')
            ->findOrFail($id);

        return $this->success(new CareRequestResource($careRequest));
    }
}
