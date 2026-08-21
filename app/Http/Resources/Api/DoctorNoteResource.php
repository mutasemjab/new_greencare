<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class DoctorNoteResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'note'       => $this->note,
            'doctor'     => $this->whenLoaded('doctor', fn () => [
                'id'   => $this->doctor->id,
                'name' => $this->doctor->name,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
