<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\XrayCategoryResource;
use App\Http\Resources\Api\XrayRequestResource;
use App\Http\Resources\Api\XrayTestResource;
use App\Http\Traits\ApiResponse;
use App\Models\XrayCategory;
use App\Models\XrayRequest;
use App\Models\XrayRequestTest;
use App\Models\XrayTest;
use Illuminate\Http\Request;

class XrayController extends Controller
{
    use ApiResponse;

    public function categories()
    {
        return $this->success(XrayCategoryResource::collection(XrayCategory::all()));
    }

    public function tests(Request $request)
    {
        $query = XrayTest::with('category');

        if ($request->filled('category_id')) {
            $query->where('xray_category_id', $request->category_id);
        }

        return $this->success(XrayTestResource::collection($query->get()));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'       => 'required|date',
            'time'       => 'required|string',
            'address_id' => 'sometimes|nullable|exists:user_addresses,id',
            'notes'      => 'sometimes|nullable|string',
            'test_ids'   => 'required|array|min:1',
            'test_ids.*' => 'exists:xray_tests,id',
        ]);

        $user = $request->user('user-api');

        $xrayRequest = XrayRequest::create([
            'user_id'      => $user->id,
            'patient_code' => $user->patient_code ?? '',
            'address_id'   => $request->address_id,
            'booking_date' => $request->date,
            'booking_time' => $request->time,
            'notes'        => $request->notes,
            'status'       => 'pending',
        ]);

        $tests = XrayTest::whereIn('id', $request->test_ids)->get();
        foreach ($tests as $test) {
            XrayRequestTest::create([
                'xray_request_id' => $xrayRequest->id,
                'xray_test_id'    => $test->id,
                'unit_price'      => $test->price,
            ]);
        }

        $total = $tests->sum('price');
        $xrayRequest->update(['total' => $total]);

        $xrayRequest->load('tests.test');

        return $this->success(new XrayRequestResource($xrayRequest), 'تم إرسال طلب الأشعة', 201);
    }

    public function index(Request $request)
    {
        $requests = XrayRequest::where('user_id', $request->user('user-api')->id)
            ->with('tests.test')
            ->latest()
            ->paginate(15);

        return $this->success(XrayRequestResource::collection($requests)->response()->getData(true));
    }

    public function show(Request $request, int $id)
    {
        $xrayRequest = XrayRequest::where('user_id', $request->user('user-api')->id)
            ->with('tests.test')
            ->findOrFail($id);

        return $this->success(new XrayRequestResource($xrayRequest));
    }
}
