<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class NursingRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'type'       => $this->whenLoaded('serviceType', fn () =>
                new NursingTypeResource($this->serviceType)
            ),
            'date'       => $this->booking_date
                ? \Carbon\Carbon::parse($this->booking_date)->format('Y-m-d')
                : null,
            'time'       => $this->booking_time,
            'address'    => $this->address_id,
            'notes'      => $this->notes,
            'status'     => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
