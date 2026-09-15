<?php

namespace App\Http\Controllers\Api\v1\Integration;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\XrayRequestResource;
use App\Http\Traits\ApiResponse;
use App\Models\XrayRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Full-admin xray-request management for external platforms authenticated
 * via API key (see VerifyApiKey middleware).
 */
class XrayRequestController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = XrayRequest::with(['user', 'tests.test'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('patient_code', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $requests = $query->paginate(20);

        return $this->success(XrayRequestResource::collection($requests)->response()->getData(true));
    }

    public function show($id)
    {
        $xrayRequest = XrayRequest::with(['user', 'address', 'tests.test'])->findOrFail($id);

        return $this->success(new XrayRequestResource($xrayRequest));
    }

    public function updateStatus(Request $httpRequest, $id)
    {
        $xrayRequest = XrayRequest::findOrFail($id);

        $httpRequest->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $xrayRequest->update(['status' => $httpRequest->status]);

        return $this->success(new XrayRequestResource($xrayRequest->fresh(['user', 'tests.test'])), 'تم تحديث الحالة');
    }

    public function uploadResult(Request $httpRequest, $id)
    {
        $xrayRequest = XrayRequest::findOrFail($id);

        $httpRequest->validate([
            'result_file' => 'required|file|mimes:pdf|max:10240',
        ], [
            'result_file.mimes' => 'يجب أن يكون الملف بصيغة PDF فقط',
        ]);

        if ($xrayRequest->result_file) {
            Storage::disk('public')->delete($xrayRequest->result_file);
        }

        $path = $httpRequest->file('result_file')->store('xray-results', 'public');

        $xrayRequest->update(['result_file' => $path]);

        return $this->success(new XrayRequestResource($xrayRequest->fresh(['user', 'tests.test'])), 'تم رفع نتيجة الأشعة');
    }
}
