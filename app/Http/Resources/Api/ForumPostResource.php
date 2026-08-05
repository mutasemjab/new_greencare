<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ForumPostResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'body'            => $this->content,
            'type'            => $this->type,
            'is_pinned'       => (bool) $this->is_pinned,
            'replies_count'   => $this->replies_count,
            'sub_category_id' => $this->forum_sub_category_id,
            'user'            => $this->whenLoaded('user', fn () => [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'phone' => $this->user->phone,
            ]),
            'replies'         => $this->whenLoaded('replies', fn () =>
                ForumReplyResource::collection($this->replies)
            ),
            'created_at'      => $this->created_at,
        ];
    }
}
