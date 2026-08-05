<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        $images = [];
        if (is_array($this->images)) {
            $images = array_map(fn ($path) => Storage::url($path), $this->images);
        }

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'price'       => (float) $this->price,
            'sale_price'  => $this->sale_price !== null ? (float) $this->sale_price : null,
            'images'      => $images,
            'category_id' => $this->store_category_id,
            'is_active'   => $this->is_active,
            'sort_order'  => $this->sort_order ?? null,
            'category'    => $this->whenLoaded('category', fn () =>
                new StoreCategoryResource($this->category)
            ),
        ];
    }
}
