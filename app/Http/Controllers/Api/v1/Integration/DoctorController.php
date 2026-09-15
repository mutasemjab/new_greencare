<?php

namespace App\Http\Controllers\Api\v1\Integration;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\DoctorResource;
use App\Http\Traits\ApiResponse;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Full-admin doctor management for external platforms authenticated via
 * API key (see VerifyApiKey middleware).
 */
class DoctorController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Doctor::latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('specialty', 'like', "%{$request->search}%");
        }

        $doctors = $query->paginate(20);

        return $this->success(DoctorResource::collection($doctors)->response()->getData(true));
    }

    public function show($id)
    {
        $doctor = Doctor::findOrFail($id);

        return $this->success(new DoctorResource($doctor));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'photo'              => 'nullable|image|max:2048',
            'specialty'          => 'required|string|max:255',
            'home_visit_price'   => 'required|numeric|min:0',
            'appointment_price'  => 'required|numeric|min:0',
            'rating'             => 'required|numeric|min:0|max:5',
            'years_experience'   => 'required|integer|min:0',
            'description'        => 'nullable|string',
            'booking_phone'      => 'nullable|string|max:20',
            'sort_order'         => 'integer|min:0',
            'is_active'          => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('doctors', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $doctor = Doctor::create($data);

        return $this->success(new DoctorResource($doctor), 'تم إضافة الطبيب', 201);
    }

    public function update(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);

        $data = $request->validate([
            'name'               => 'sometimes|string|max:255',
            'photo'              => 'nullable|image|max:2048',
            'specialty'          => 'sometimes|string|max:255',
            'home_visit_price'   => 'sometimes|numeric|min:0',
            'appointment_price'  => 'sometimes|numeric|min:0',
            'rating'             => 'sometimes|numeric|min:0|max:5',
            'years_experience'   => 'sometimes|integer|min:0',
            'description'        => 'nullable|string',
            'booking_phone'      => 'nullable|string|max:20',
            'sort_order'         => 'integer|min:0',
            'is_active'          => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            if ($doctor->photo) {
                Storage::disk('public')->delete($doctor->photo);
            }
            $data['photo'] = $request->file('photo')->store('doctors', 'public');
        }

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $doctor->update($data);

        return $this->success(new DoctorResource($doctor->fresh()), 'تم تعديل الطبيب');
    }

    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);

        if ($doctor->photo) {
            Storage::disk('public')->delete($doctor->photo);
        }
        $doctor->delete();

        return $this->success(null, 'تم حذف الطبيب');
    }
}
