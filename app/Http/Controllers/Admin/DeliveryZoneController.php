<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('delivery-zone-table')) {
            abort(403);
        }

        $query = DeliveryZone::latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $deliveryZones = $query->paginate(20)->withQueryString();

        return view('admin.delivery_zones.index', compact('deliveryZones'));
    }

    public function create()
    {
        if (!auth()->user()->can('delivery-zone-add')) {
            abort(403);
        }

        return view('admin.delivery_zones.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('delivery-zone-add')) {
            abort(403);
        }

        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'fee'       => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        DeliveryZone::create($data);

        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'تم إضافة منطقة التوصيل بنجاح');
    }

    public function edit(DeliveryZone $deliveryZone)
    {
        if (!auth()->user()->can('delivery-zone-edit')) {
            abort(403);
        }

        return view('admin.delivery_zones.edit', compact('deliveryZone'));
    }

    public function update(Request $request, DeliveryZone $deliveryZone)
    {
        if (!auth()->user()->can('delivery-zone-edit')) {
            abort(403);
        }

        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'fee'       => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', false);

        $deliveryZone->update($data);

        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'تم تعديل منطقة التوصيل بنجاح');
    }

    public function destroy(DeliveryZone $deliveryZone)
    {
        if (!auth()->user()->can('delivery-zone-delete')) {
            abort(403);
        }

        $deliveryZone->delete();

        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'تم حذف منطقة التوصيل');
    }
}
