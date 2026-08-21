<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomAttachmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'url'           => $this->url,
            'original_name' => $this->original_name,
            'created_at'    => $this->created_at,
        ];
    }
}
