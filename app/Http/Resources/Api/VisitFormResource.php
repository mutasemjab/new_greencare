<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class VisitFormResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'patient_name' => $this->whenLoaded('patient', fn () => $this->patient->name),
            'created_at'   => $this->created_at,
            'answers'      => $this->whenLoaded('answers', fn () =>
                VisitFormAnswerResource::collection($this->answers->values())
            ),
            'attachments'  => $this->whenLoaded('attachments', fn () =>
                $this->attachments->map(fn ($a) => ['id' => $a->id, 'url' => $a->url])->values()
            ),
        ];
    }
}
