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
            'status'     => $this->status,
            'room_id'    => $this->room_id ?? null,
            'tests'      => $this->whenLoaded('tests', fn () =>
                $this->tests->map(fn ($pivot) => [
                    'id'         => $pivot->lab_test_id,
                    'name'       => optional($pivot->test)->name,
                    'unit_price' => (float) $pivot->unit_price,
                ])
            ),
            'created_at' => $this->created_at,
        ];
    }
}
