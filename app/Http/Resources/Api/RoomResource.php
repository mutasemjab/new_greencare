<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'description'      => $this->description,
            'address'          => $this->address,
            'discount_value'   => $this->discount_value,
            'is_active'        => (bool) $this->is_active,
            'firebase_room_id' => $this->firebase_room_id,
            'patient'          => $this->whenLoaded('patient', fn () =>
                new UserResource($this->patient)
            ),
            'members'          => $this->whenLoaded('members', fn () =>
                RoomMemberResource::collection($this->members)
            ),
            'created_at'       => $this->created_at,
        ];
    }
}
