<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class DoctorOrderReplyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'reply_text' => $this->reply_text,
            'nurse'      => $this->whenLoaded('nurse', fn () => [
                'id'   => $this->nurse->id,
                'name' => $this->nurse->name,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
