<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class XrayTestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'price'       => (float) $this->price,
            'category'    => $this->whenLoaded('category', fn () =>
                new XrayCategoryResource($this->category)
            ),
        ];
    }
}
