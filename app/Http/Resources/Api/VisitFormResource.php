<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class VisitFormResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'code'           => $this->code,
            'discount_value' => (float) $this->discount_value,
            'patient_name'   => $this->whenLoaded('patient', fn () => $this->patient->name),
            'created_at'     => $this->created_at,
            'answers'        => $this->whenLoaded('answers', fn () =>
                VisitFormAnswerResource::collection($this->answers->values())
            ),
            'attachments'    => $this->whenLoaded('attachments', fn () =>
                $this->attachments->map(fn ($a) => ['id' => $a->id, 'url' => $a->url])->values()
            ),
            'lab_results'    => $this->whenLoaded('labRequests', fn () =>
                LabRequestResource::collection($this->labRequests->values())
            ),
            'xray_results'   => $this->whenLoaded('xrayRequests', fn () =>
                XrayRequestResource::collection($this->xrayRequests->values())
            ),
            'orders'         => $this->whenLoaded('orders', fn () =>
                OrderResource::collection($this->orders->values())
            ),
        ];
    }
}
