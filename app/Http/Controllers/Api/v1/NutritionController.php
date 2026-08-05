<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NutritionRequestResource;
use App\Http\Traits\ApiResponse;
use App\Models\NutritionRequest;
use Illuminate\Http\Request;

class NutritionController extends Controller
{
    use ApiResponse;

    public function store(Request $request)
    {
        $request->validate([
            'chronic_diseases'    => 'sometimes|nullable|string',
            'food_allergies'      => 'sometimes|nullable|string',
            'medicine_allergies'  => 'sometimes|nullable|string',
            'current_medications' => 'sometimes|nullable|string',
            'height'              => 'required|numeric|min:1',
            'weight'              => 'required|numeric|min:1',
            'goal'                => 'required|in:lose_weight,gain_weight,maintain',
            'notes'               => 'sometimes|nullable|string',
        ]);

        $height = (float) $request->height;
        $weight = (float) $request->weight;
        $bmi    = round($weight / (($height / 100) ** 2), 2);

        $nutrition = NutritionRequest::create([
            'user_id'             => $request->user('user-api')->id,
            'chronic_diseases'    => $request->chronic_diseases,
            'food_allergies'      => $request->food_allergies,
            'medicine_allergies'  => $request->medicine_allergies,
            'current_medications' => $request->current_medications,
            'height'              => $height,
            'weight'              => $weight,
            'bmi'                 => $bmi,
            'goal'                => $request->goal,
            'notes'               => $request->notes,
            'status'              => 'pending',
        ]);

        return $this->success(new NutritionRequestResource($nutrition), 'تم إرسال طلب التغذية', 201);
    }

    public function index(Request $request)
    {
        $requests = NutritionRequest::where('user_id', $request->user('user-api')->id)
            ->latest()
            ->paginate(15);

        return $this->success(NutritionRequestResource::collection($requests)->response()->getData(true));
    }

    public function show(Request $request, int $id)
    {
        $nutrition = NutritionRequest::where('user_id', $request->user('user-api')->id)
            ->findOrFail($id);

        return $this->success(new NutritionRequestResource($nutrition));
    }
}
