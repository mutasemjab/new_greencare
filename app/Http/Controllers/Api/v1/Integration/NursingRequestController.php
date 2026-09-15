<?php

namespace App\Http\Controllers\Api\v1\Integration;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NursingRequestResource;
use App\Http\Traits\ApiResponse;
use App\Models\NursingRequest;
use Illuminate\Http\Request;

/**
 * Full-admin nursing-request management for external platforms
 * authenticated via API key (see VerifyApiKey middleware).
 */
class NursingRequestController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = NursingRequest::with(['user', 'serviceType'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('patient_code', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $requests = $query->paginate(20);

        return $this->success(NursingRequestResource::collection($requests)->response()->getData(true));
    }

    public function show($id)
    {
        $nursingRequest = NursingRequest::with(['user', 'serviceType', 'address'])->findOrFail($id);

        return $this->success(new NursingRequestResource($nursingRequest));
    }

    public function updateStatus(Request $httpRequest, $id)
    {
        $nursingRequest = NursingRequest::findOrFail($id);

        $httpRequest->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $nursingRequest->update(['status' => $httpRequest->status]);

        return $this->success(new NursingRequestResource($nursingRequest->fresh(['user', 'serviceType'])), 'تم تحديث الحالة');
    }
}
