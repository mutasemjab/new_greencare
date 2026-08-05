<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class CareRequestResource extends JsonResource
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
            'services'   => $this->whenLoaded('services', fn () =>
                $this->services->map(fn ($pivot) => [
                    'id'         => $pivot->care_service_id,
                    'name'       => optional($pivot->service)->name,
                    'unit_price' => (float) $pivot->unit_price,
                ])
            ),
            'created_at' => $this->created_at,
        ];
    }
}
