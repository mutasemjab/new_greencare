<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class VisitFormAnswerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'field_question'    => $this->field_question,
            'field_answer_type' => $this->field_type,
            'display_answer'    => $this->display_answer,
        ];
    }
}
