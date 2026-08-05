<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientTransferResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'from_location'    => $this->from_location,
            'from_lat'         => $this->from_latitude,
            'from_lng'         => $this->from_longitude,
            'to_location'      => $this->to_location,
            'to_lat'           => $this->to_latitude,
            'to_lng'           => $this->to_longitude,
            'case_description' => $this->case_description,
            'status'           => $this->status,
            'from_zone'        => $this->whenLoaded('fromZone', fn () => [
                'id'   => $this->fromZone->id,
                'name' => $this->fromZone->name,
            ]),
            'to_zone'          => $this->whenLoaded('toZone', fn () => [
                'id'   => $this->toZone->id,
                'name' => $this->toZone->name,
            ]),
            'created_at'       => $this->created_at,
        ];
    }
}
