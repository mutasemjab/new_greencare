<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\LabRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequestController extends Controller
{
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

        $requests = $query->paginate(20)->withQueryString();

        return view('lab.requests.index', compact('requests'));
    }

    public function show(LabRequest $labRequest)
    {
        $labRequest->load(['user', 'address', 'tests.test.category']);

        return view('lab.requests.show', ['request' => $labRequest]);
    }

    public function updateStatus(Request $httpRequest, LabRequest $labRequest)
    {
        $httpRequest->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $labRequest->update(['status' => $httpRequest->status]);

        return back()->with('success', 'تم تحديث الحالة بنجاح');
    }

    public function uploadResult(Request $httpRequest, LabRequest $labRequest)
    {
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

        return back()->with('success', 'تم رفع نتيجة التحليل بنجاح');
    }
}
