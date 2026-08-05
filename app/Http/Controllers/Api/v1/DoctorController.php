<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\DoctorBookingResource;
use App\Http\Resources\Api\DoctorResource;
use App\Http\Traits\ApiResponse;
use App\Models\Doctor;
use App\Models\DoctorBooking;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Doctor::where('is_active', true);

        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('specialty', 'like', $term);
            });
        }

        $doctors = $query->paginate(15);

        return $this->success(DoctorResource::collection($doctors)->response()->getData(true));
    }

    public function show(int $id)
    {
        $doctor = Doctor::where('is_active', true)->findOrFail($id);

        return $this->success(new DoctorResource($doctor));
    }

    public function book(Request $request, int $doctorId)
    {
        $request->validate([
            'visit_type'   => 'required|in:home_visit,appointment',
            'booking_date' => 'required|date',
            'booking_time' => 'required|string',
            'notes'        => 'sometimes|nullable|string',
        ]);

        $doctor = Doctor::where('is_active', true)->findOrFail($doctorId);

        $price = $request->visit_type === 'home_visit'
            ? $doctor->home_visit_price
            : $doctor->appointment_price;

        $booking = DoctorBooking::create([
            'user_id'      => $request->user('user-api')->id,
            'doctor_id'    => $doctor->id,
            'visit_type'   => $request->visit_type,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'price'        => $price,
            'notes'        => $request->notes,
            'status'       => 'pending',
        ]);

        $booking->load('doctor');

        return $this->success(new DoctorBookingResource($booking), 'تم إرسال طلب الحجز', 201);
    }

    public function bookings(Request $request)
    {
        $bookings = DoctorBooking::where('user_id', $request->user('user-api')->id)
            ->with('doctor')
            ->latest()
            ->paginate(15);

        return $this->success(DoctorBookingResource::collection($bookings)->response()->getData(true));
    }

    public function bookingShow(Request $request, int $id)
    {
        $booking = DoctorBooking::where('user_id', $request->user('user-api')->id)
            ->with('doctor')
            ->findOrFail($id);

        return $this->success(new DoctorBookingResource($booking));
    }
}
