<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\LabCategoryResource;
use App\Http\Resources\Api\LabRequestResource;
use App\Http\Resources\Api\LabTestResource;
use App\Http\Traits\ApiResponse;
use App\Models\LabCategory;
use App\Models\LabRequest;
use App\Models\LabRequestTest;
use App\Models\LabTest;
use Illuminate\Http\Request;

class LabController extends Controller
{
    use ApiResponse;

    public function categories()
    {
        return $this->success(LabCategoryResource::collection(LabCategory::all()));
    }

    public function tests(Request $request)
    {
        $query = LabTest::with('category');

        if ($request->filled('category_id')) {
            $query->where('lab_category_id', $request->category_id);
        }

        return $this->success(LabTestResource::collection($query->get()));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'       => 'required|date',
            'time'       => 'required|string',
            'address_id' => 'sometimes|nullable|exists:user_addresses,id',
            'notes'      => 'sometimes|nullable|string',
            'test_ids'   => 'required|array|min:1',
            'test_ids.*' => 'exists:lab_tests,id',
        ]);

        $user = $request->user('user-api');

        $labRequest = LabRequest::create([
            'user_id'      => $user->id,
            'patient_code' => $user->currentRoom()?->patient_code ?? '',
            'address_id'   => $request->address_id,
            'booking_date' => $request->date,
            'booking_time' => $request->time,
            'notes'        => $request->notes,
            'status'       => 'pending',
        ]);

        $tests = LabTest::whereIn('id', $request->test_ids)->get();
        foreach ($tests as $test) {
            LabRequestTest::create([
                'lab_request_id' => $labRequest->id,
                'lab_test_id'    => $test->id,
                'unit_price'     => $test->price,
            ]);
        }

        $total = $tests->sum('price');
        $labRequest->update(['total' => $total]);

        $labRequest->load('tests.test');

        return $this->success(new LabRequestResource($labRequest), 'تم إرسال طلب التحاليل', 201);
    }

    public function index(Request $request)
    {
        $requests = LabRequest::where('user_id', $request->user('user-api')->id)
            ->with('tests.test')
            ->latest()
            ->paginate(15);

        return $this->success(LabRequestResource::collection($requests)->response()->getData(true));
    }

    public function show(Request $request, int $id)
    {
        $labRequest = LabRequest::where('user_id', $request->user('user-api')->id)
            ->with('tests.test')
            ->findOrFail($id);

        return $this->success(new LabRequestResource($labRequest));
    }
}
