<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class BathingRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'date'         => $this->booking_date
                ? \Carbon\Carbon::parse($this->booking_date)->format('Y-m-d')
                : null,
            'time'         => $this->booking_time,
            'notes'        => $this->notes,
            'payment_type' => $this->payment_type,
            'status'       => $this->status,
            'created_at'   => $this->created_at,
        ];
    }
}
