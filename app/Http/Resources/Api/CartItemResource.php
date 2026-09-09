<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'product_id' => (int) $this->product_id,
            'product'    => $this->whenLoaded('product', fn () =>
                new ProductResource($this->product)
            ),
            'quantity'   => (float) $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'subtotal'   => (float) ($this->unit_price * $this->quantity),
        ];
    }
}
