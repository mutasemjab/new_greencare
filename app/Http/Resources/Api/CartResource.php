<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'items'       => CartItemResource::collection($this->whenLoaded('items')),
            'total'       => (float) $this->total,
            'items_count' => $this->items_count,
        ];
    }
}
