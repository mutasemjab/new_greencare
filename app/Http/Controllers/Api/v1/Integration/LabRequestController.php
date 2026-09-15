<?php

namespace App\Http\Controllers\Api\v1\Integration;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\LabRequestResource;
use App\Http\Traits\ApiResponse;
use App\Models\LabRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Full-admin lab-request management for external platforms authenticated
 * via API key (see VerifyApiKey middleware).
 */
class LabRequestController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = LabRequest::with(['user', 'tests.test'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('patient_code', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $requests = $query->paginate(20);

        return $this->success(LabRequestResource::collection($requests)->response()->getData(true));
    }

    public function show($id)
    {
        $labRequest = LabRequest::with(['user', 'address', 'tests.test'])->findOrFail($id);

        return $this->success(new LabRequestResource($labRequest));
    }

    public function updateStatus(Request $httpRequest, $id)
    {
        $labRequest = LabRequest::findOrFail($id);

        $httpRequest->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $labRequest->update(['status' => $httpRequest->status]);

        return $this->success(new LabRequestResource($labRequest->fresh(['user', 'tests.test'])), 'تم تحديث الحالة');
    }

    public function uploadResult(Request $httpRequest, $id)
    {
        $labRequest = LabRequest::findOrFail($id);

        $httpRequest->validate([
            'result_file' => 'required|file|mimes:pdf|max:10240',
        ], [
            'result_file.mimes' => 'يجب أن يكون الملف بصيغة PDF فقط',
        ]);

        if ($labRequest->result_file) {
            Storage::disk('public')->delete($labRequest->result_file);
        }

        $path = $httpRequest->file('result_file')->store('lab-results', 'public');

        $labRequest->update(['result_file' => $path]);

        return $this->success(new LabRequestResource($labRequest->fresh(['user', 'tests.test'])), 'تم رفع نتيجة التحليل');
    }
}
