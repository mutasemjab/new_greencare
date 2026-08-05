<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'product_id'   => $this->product_id,
            'product_name' => $this->product_name,
            'unit_price'   => (float) $this->unit_price,
            'quantity'     => $this->quantity,
            'total'        => (float) $this->total,
        ];
    }
}
