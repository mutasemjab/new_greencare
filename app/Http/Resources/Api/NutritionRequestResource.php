<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class NutritionRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                   => $this->id,
            'chronic_diseases'     => $this->chronic_diseases,
            'food_allergies'       => $this->food_allergies,
            'medicine_allergies'   => $this->medicine_allergies,
            'current_medications'  => $this->current_medications,
            'height'               => $this->height !== null ? (float) $this->height : null,
            'weight'               => $this->weight !== null ? (float) $this->weight : null,
            'bmi'                  => $this->bmi !== null ? (float) $this->bmi : null,
            'goal'                 => $this->goal,
            'notes'                => $this->notes,
            'status'               => $this->status,
            'created_at'           => $this->created_at,
        ];
    }
}
