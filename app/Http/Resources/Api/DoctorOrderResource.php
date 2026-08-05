<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class DoctorOrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'order_text'  => $this->order_text,
            'is_executed' => (bool) $this->is_executed,
            'executed_at' => $this->executed_at,
            'doctor'      => $this->whenLoaded('doctor', fn () => [
                'id'   => $this->doctor->id,
                'name' => $this->doctor->name,
            ]),
            'replies'     => $this->whenLoaded('replies', fn () =>
                DoctorOrderReplyResource::collection($this->replies)
            ),
            'created_at'  => $this->created_at,
        ];
    }
}
