<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class DoctorBookingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'doctor'       => $this->whenLoaded('doctor', fn () =>
                new DoctorResource($this->doctor)
            ),
            'visit_type'   => $this->visit_type,
            'booking_date' => $this->booking_date
                ? \Carbon\Carbon::parse($this->booking_date)->format('Y-m-d')
                : null,
            'booking_time' => $this->booking_time,
            'price'        => (float) $this->price,
            'notes'        => $this->notes,
            'status'       => $this->status,
            'created_at'   => $this->created_at,
        ];
    }
}
