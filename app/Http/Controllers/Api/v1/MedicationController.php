<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\MedicationResource;
use App\Http\Traits\ApiResponse;
use App\Models\Medication;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $medications = Medication::where('user_id', $request->user('user-api')->id)
            ->latest()
            ->paginate(20);

        return $this->success(MedicationResource::collection($medications)->response()->getData(true));
    }

    public function store(Request $request)
    {
        $request->validate([
            'medication_name' => 'required|string|max:255',
            'dosage'          => 'required|string|max:255',
            'route'           => 'required|in:oral,iv,im,subcutaneous,topical,inhalation,other',
            'frequency'       => 'required|string|max:255',
            'times'           => 'sometimes|nullable|array',
            'times.*'         => 'date_format:H:i',
            'start_date'      => 'required|date',
            'end_date'        => 'nullable|date|after:start_date',
            'notes'           => 'sometimes|nullable|string',
        ]);

        $medication = Medication::create([
            'user_id'         => $request->user('user-api')->id,
            'medication_name' => $request->medication_name,
            'dosage'          => $request->dosage,
            'route'           => $request->route,
            'frequency'       => $request->frequency,
            'times'           => $request->times,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'notes'           => $request->notes,
        ]);

        return $this->success(new MedicationResource($medication), 'تم إضافة الدواء', 201);
    }

    public function destroy(Request $request, int $id)
    {
        $medication = Medication::where('user_id', $request->user('user-api')->id)
            ->findOrFail($id);

        $medication->delete();

        return $this->success(null, 'تم حذف الدواء');
    }
}
