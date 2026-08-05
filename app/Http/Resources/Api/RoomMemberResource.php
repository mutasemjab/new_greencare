<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomMemberResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'      => $this->id,
            'user_id' => $this->user_id,
            'role'    => $this->role,
            'user'    => $this->whenLoaded('user', fn () => [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'phone' => $this->user->phone,
            ]),
        ];
    }
}
