<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BannerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'image_url'  => $this->image ? Storage::disk('public')->url($this->image) : null,
            'link'       => $this->url ?? $this->link ?? null,
            'section'    => $this->section,
            'sort_order' => $this->sort_order,
        ];
    }
}
