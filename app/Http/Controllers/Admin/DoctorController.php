<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $query = Doctor::latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('specialty', 'like', "%{$request->search}%");
        }

        $doctors = $query->paginate(20)->withQueryString();

        return view('admin.doctors.index', compact('doctors'));
    }

    public function create()
    {
        return view('admin.doctors.create');
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

        Doctor::create($data);

        return redirect()->route('admin.doctors.index')
            ->with('success', 'تم إضافة الطبيب بنجاح');
    }

    public function show(Doctor $doctor)
    {
        $bookings = DoctorBooking::with('user')
            ->where('doctor_id', $doctor->id)
            ->latest()
            ->paginate(15);

        return view('admin.doctors.show', compact('doctor', 'bookings'));
    }

    public function edit(Doctor $doctor)
    {
        return view('admin.doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor)
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
            if ($doctor->photo) {
                Storage::disk('public')->delete($doctor->photo);
            }
            $data['photo'] = $request->file('photo')->store('doctors', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', false);

        $doctor->update($data);

        return redirect()->route('admin.doctors.index')
            ->with('success', 'تم تعديل الطبيب بنجاح');
    }

    public function destroy(Doctor $doctor)
    {
        if ($doctor->photo) {
            Storage::disk('public')->delete($doctor->photo);
        }
        $doctor->delete();

        return redirect()->route('admin.doctors.index')
            ->with('success', 'تم حذف الطبيب');
    }

    // ── Bookings ──────────────────────────────────────────────────────────

    public function bookings(Request $request)
    {
        $query = DoctorBooking::with(['user', 'doctor'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('visit_type')) {
            $query->where('visit_type', $request->visit_type);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"))
                  ->orWhereHas('doctor', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $bookings = $query->paginate(20)->withQueryString();

        return view('admin.doctors.bookings', compact('bookings'));
    }

    public function updateBookingStatus(Request $request, DoctorBooking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $booking->update(['status' => $request->status]);

        return back()->with('success', 'تم تحديث الحالة بنجاح');
    }
}
