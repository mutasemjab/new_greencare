<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomReportResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'report_type'  => $this->report_type,
            'report_hour'  => $this->report_hour,
            'submitted_at' => $this->submitted_at,
            'submitted_by' => $this->whenLoaded('submittedBy', fn () => [
                'id'   => $this->submittedBy->id,
                'name' => $this->submittedBy->name,
            ]),
            'answers'      => $this->whenLoaded('answers', fn () =>
                RoomReportAnswerResource::collection($this->answers)
            ),
        ];
    }
}
