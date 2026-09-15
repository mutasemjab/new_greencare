<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class BathingRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'code'         => $this->bathingCard?->code,
            'date'         => $this->booking_date
                ? \Carbon\Carbon::parse($this->booking_date)->format('Y-m-d')
                : null,
            'time'         => $this->booking_time,
            'notes'        => $this->notes,
            'payment_type' => $this->payment_type,
            'status'       => $this->status,
            'user'         => $this->whenLoaded('user', fn () =>
                new UserResource($this->user)
            ),
            'created_at'   => $this->created_at,
        ];
    }
}
