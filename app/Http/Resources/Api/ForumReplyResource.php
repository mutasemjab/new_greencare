<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ForumReplyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'body'       => $this->content,
            'user'       => $this->whenLoaded('user', fn () => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
