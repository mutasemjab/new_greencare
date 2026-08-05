<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'status'         => $this->status,
            'payment_status' => $this->payment_status,
            'subtotal'       => (float) $this->subtotal,
            'delivery_fee'   => (float) $this->delivery_fee,
            'total'          => (float) $this->total,
            'notes'          => $this->notes,
            'room_id'        => $this->room_id ?? null,
            'items'          => OrderItemResource::collection($this->whenLoaded('items')),
            'address'        => $this->whenLoaded('address', fn () =>
                new AddressResource($this->address)
            ),
            'created_at'     => $this->created_at,
        ];
    }
}
