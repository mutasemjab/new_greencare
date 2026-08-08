<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NursingRequest;
use App\Models\NursingServiceType;
use Illuminate\Http\Request;

class NursingController extends Controller
{
    // ── Service Types ─────────────────────────────────────────────────────

    public function types(Request $request)
    {
        $query = NursingServiceType::orderBy('sort_order');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $types = $query->paginate(20)->withQueryString();

        return view('admin.nursing.types.index', compact('types'));
    }

    public function createType()
    {
        return view('admin.nursing.types.create');
    }

    public function storeType(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'icon'       => 'nullable|image|max:1024',
            'price'      => 'required|numeric|min:0',
            'sort_order' => 'integer|min:0',
            'is_active'  => 'boolean',
        ]);

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('nursing/icons', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        NursingServiceType::create($data);

        return redirect()->route('admin.nursing.types')
            ->with('success', 'تم إضافة نوع الخدمة بنجاح');
    }

    public function editType(NursingServiceType $type)
    {
        return view('admin.nursing.types.edit', compact('type'));
    }

    public function updateType(Request $request, NursingServiceType $type)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'icon'       => 'nullable|image|max:1024',
            'price'      => 'required|numeric|min:0',
            'sort_order' => 'integer|min:0',
            'is_active'  => 'boolean',
        ]);

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('nursing/icons', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', false);

        $type->update($data);

        return redirect()->route('admin.nursing.types')
            ->with('success', 'تم تعديل نوع الخدمة بنجاح');
    }

    public function destroyType(NursingServiceType $type)
    {
        $type->delete();

        return redirect()->route('admin.nursing.types')
            ->with('success', 'تم حذف نوع الخدمة');
    }

    // ── Requests ──────────────────────────────────────────────────────────

    public function requests(Request $request)
    {
        $query = NursingRequest::with(['user', 'serviceType'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('patient_code', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('admin.nursing.requests.index', compact('requests'));
    }

    public function showRequest(NursingRequest $request)
    {
        $request->load(['user', 'serviceType', 'address.deliveryZone']);

        return view('admin.nursing.requests.show', compact('request'));
    }

    public function updateRequestStatus(Request $httpRequest, NursingRequest $request)
    {
        $httpRequest->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $request->update(['status' => $httpRequest->status]);

        return back()->with('success', 'تم تحديث الحالة بنجاح');
    }
}
