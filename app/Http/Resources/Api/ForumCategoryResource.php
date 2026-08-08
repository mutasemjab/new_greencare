<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ForumCategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description ?? null,
            'image_url'   => isset($this->image) && $this->image
                ? Storage::disk('public')->url($this->image)
                : (isset($this->icon) && $this->icon ? Storage::disk('public')->url($this->icon) : null),
        ];
    }
}
