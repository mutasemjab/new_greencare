<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    public function index()
    {
        $zones = DeliveryZone::latest()->get();

        return view('admin.delivery_zones.index', compact('zones'));
    }

    public function create()
    {
        return view('admin.delivery_zones.create');
    }

    public function store(Request $request)
    {
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
        return view('admin.delivery_zones.edit', compact('deliveryZone'));
    }

    public function update(Request $request, DeliveryZone $deliveryZone)
    {
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
        $deliveryZone->delete();

        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'تم حذف منطقة التوصيل');
    }
}
