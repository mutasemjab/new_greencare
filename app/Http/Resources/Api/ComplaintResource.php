<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'room_id'        => $this->room_id,
            'patient_id'     => $this->patient_id,
            'complaint_text' => $this->complaint_text,
            'status'         => $this->status,
            'created_at'     => $this->created_at,
        ];
    }
}
