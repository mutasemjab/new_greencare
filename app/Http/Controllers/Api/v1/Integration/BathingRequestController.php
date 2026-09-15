<?php

namespace App\Http\Controllers\Api\v1\Integration;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BathingRequestResource;
use App\Http\Traits\ApiResponse;
use App\Models\BathingRequest;
use Illuminate\Http\Request;

/**
 * Full-admin bathing-request management for external platforms
 * authenticated via API key (see VerifyApiKey middleware).
 */
class BathingRequestController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = BathingRequest::with(['user', 'bathingCard', 'pointOfSale'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('search')) {
            $query->where('patient_code', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $requests = $query->paginate(20);

        return $this->success(BathingRequestResource::collection($requests)->response()->getData(true));
    }

    public function show($id)
    {
        $bathingRequest = BathingRequest::with(['user', 'bathingCard.pointOfSale', 'pointOfSale', 'address'])->findOrFail($id);

        return $this->success(new BathingRequestResource($bathingRequest));
    }

    public function updateStatus(Request $httpRequest, $id)
    {
        $bathingRequest = BathingRequest::findOrFail($id);

        $httpRequest->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $bathingRequest->update(['status' => $httpRequest->status]);

        return $this->success(new BathingRequestResource($bathingRequest->fresh(['user', 'bathingCard'])), 'تم تحديث الحالة');
    }
}
