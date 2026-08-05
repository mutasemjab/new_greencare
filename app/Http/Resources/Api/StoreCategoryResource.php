<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class StoreCategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'image_url' => $this->image ? Storage::url($this->image) : null,
            'parent_id' => $this->parent_id,
            'children'  => $this->whenLoaded('children', fn () =>
                StoreCategoryResource::collection($this->children)
            ),
        ];
    }
}
