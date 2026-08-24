<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomReportAnswerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'field_question'   => $this->field_question,
            'field_answer_type'=> $this->field_answer_type,
            'display_answer'   => $this->display_answer,
            'image_url'        => $this->answer_image_url,
        ];
    }
}
