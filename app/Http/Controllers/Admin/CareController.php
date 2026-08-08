<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareRequest;
use App\Models\CareService;
use Illuminate\Http\Request;

class CareController extends Controller
{
    // ── Services ──────────────────────────────────────────────────────────

    public function services(Request $request)
    {
        $query = CareService::orderBy('sort_order');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $services = $query->paginate(20)->withQueryString();

        return view('admin.care.services.index', compact('services'));
    }

    public function createService()
    {
        return view('admin.care.services.create');
    }

    public function storeService(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'icon'       => 'nullable|image|max:1024',
            'price'      => 'required|numeric|min:0',
            'sort_order' => 'integer|min:0',
            'is_active'  => 'boolean',
        ]);

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('care/icons', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        CareService::create($data);

        return redirect()->route('admin.care.services')
            ->with('success', 'تم إضافة الخدمة بنجاح');
    }

    public function editService(CareService $service)
    {
        return view('admin.care.services.edit', compact('service'));
    }

    public function updateService(Request $request, CareService $service)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'icon'       => 'nullable|image|max:1024',
            'price'      => 'required|numeric|min:0',
            'sort_order' => 'integer|min:0',
            'is_active'  => 'boolean',
        ]);

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('care/icons', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', false);

        $service->update($data);

        return redirect()->route('admin.care.services')
            ->with('success', 'تم تعديل الخدمة بنجاح');
    }

    public function destroyService(CareService $service)
    {
        $service->delete();

        return redirect()->route('admin.care.services')
            ->with('success', 'تم حذف الخدمة');
    }

    // ── Requests ──────────────────────────────────────────────────────────

    public function requests(Request $request)
    {
        $query = CareRequest::with(['user', 'services.service'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('patient_code', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('admin.care.requests.index', compact('requests'));
    }

    public function showRequest(CareRequest $request)
    {
        $request->load(['user', 'address', 'services.service']);

        return view('admin.care.requests.show', compact('request'));
    }

    public function updateRequestStatus(Request $httpRequest, CareRequest $request)
    {
        $httpRequest->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $request->update(['status' => $httpRequest->status]);

        return back()->with('success', 'تم تحديث الحالة بنجاح');
    }
}
