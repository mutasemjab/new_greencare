<?php

namespace App\Http\Controllers\Api\v1\Integration;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CareRequestResource;
use App\Http\Traits\ApiResponse;
use App\Models\CareRequest;
use Illuminate\Http\Request;

/**
 * Full-admin care-request management for external platforms authenticated
 * via API key (see VerifyApiKey middleware).
 */
class CareRequestController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = CareRequest::with(['user', 'services.service'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('patient_code', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $requests = $query->paginate(20);

        return $this->success(CareRequestResource::collection($requests)->response()->getData(true));
    }

    public function show($id)
    {
        $careRequest = CareRequest::with(['user', 'address', 'services.service'])->findOrFail($id);

        return $this->success(new CareRequestResource($careRequest));
    }

    public function updateStatus(Request $httpRequest, $id)
    {
        $careRequest = CareRequest::findOrFail($id);

        $httpRequest->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $careRequest->update(['status' => $httpRequest->status]);

        return $this->success(new CareRequestResource($careRequest->fresh(['user', 'services.service'])), 'تم تحديث الحالة');
    }
}
