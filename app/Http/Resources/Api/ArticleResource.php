<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ArticleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'description'  => $this->description,
            'image_url'    => $this->image ? Storage::disk('public')->url($this->image) : null,
            'published_at' => $this->published_at
                ? \Carbon\Carbon::parse($this->published_at)->format('Y-m-d H:i')
                : null,
        ];
    }
}
