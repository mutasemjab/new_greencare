<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'label'            => $this->label,
            'address'          => $this->address,
            'city'             => $this->city,
            'latitude'         => $this->latitude,
            'longitude'        => $this->longitude,
            'is_default'       => (bool) $this->is_default,
            'delivery_zone_id' => $this->delivery_zone_id,
            'delivery_zone'    => $this->whenLoaded('deliveryZone', fn () => [
                'id'   => $this->deliveryZone->id,
                'name' => $this->deliveryZone->name,
                'fee'  => (float) $this->deliveryZone->fee,
            ]),
        ];
    }
}
