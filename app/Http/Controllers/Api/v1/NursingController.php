<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NursingRequestResource;
use App\Http\Resources\Api\NursingTypeResource;
use App\Http\Traits\ApiResponse;
use App\Models\NursingRequest;
use App\Models\NursingServiceType;
use Illuminate\Http\Request;

class NursingController extends Controller
{
    use ApiResponse;

    public function types()
    {
        $types = NursingServiceType::where('is_active', true)->get();

        return $this->success(NursingTypeResource::collection($types));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type_id'    => 'required|exists:nursing_service_types,id',
            'date'       => 'required|date',
            'time'       => 'required|string',
            'address_id' => 'sometimes|nullable|exists:user_addresses,id',
            'notes'      => 'sometimes|nullable|string',
        ]);

        $user = $request->user('user-api');

        $nursing = NursingRequest::create([
            'user_id'                 => $user->id,
            'patient_code'            => $user->patient_code ?? '',
            'nursing_service_type_id' => $request->type_id,
            'address_id'              => $request->address_id,
            'booking_date'            => $request->date,
            'booking_time'            => $request->time,
            'notes'                   => $request->notes,
            'status'                  => 'pending',
        ]);

        $nursing->load('serviceType');

        return $this->success(new NursingRequestResource($nursing), 'تم إرسال طلب التمريض', 201);
    }

    public function index(Request $request)
    {
        $requests = NursingRequest::where('user_id', $request->user('user-api')->id)
            ->with('serviceType')
            ->latest()
            ->paginate(15);

        return $this->success(NursingRequestResource::collection($requests)->response()->getData(true));
    }

    public function show(Request $request, $id)
    {
        $nursing = NursingRequest::where('user_id', $request->user('user-api')->id)
            ->with('serviceType')
            ->findOrFail($id);

        return $this->success(new NursingRequestResource($nursing));
    }
}
