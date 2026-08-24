<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class LabRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'date'       => $this->booking_date
                ? \Carbon\Carbon::parse($this->booking_date)->format('Y-m-d')
                : null,
            'time'       => $this->booking_time,
            'address'    => $this->address_id,
            'notes'      => $this->notes,
            'status'       => $this->status,
            'status_label' => $this->status_label,
            'total'      => (float) $this->total,
            'room_id'    => $this->room_id ?? null,
            'result_file_url' => $this->result_file_url,
            'result_uploaded_at' => $this->result_file ? $this->updated_at : null,
            'tests'      => $this->whenLoaded('tests', fn () =>
                $this->tests->map(fn ($pivot) => [
                    'id'         => $pivot->lab_test_id,
                    'name'       => optional($pivot->test)->name,
                    'unit_price' => (float) $pivot->unit_price,
                ])->values()
            ),
            'created_at' => $this->created_at,
        ];
    }
}
