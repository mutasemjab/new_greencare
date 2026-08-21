<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'description'        => $this->description,
            'address'            => $this->address,
            'discount_value'     => $this->discount_value,
            'is_active'          => (bool) $this->is_active,
            'firebase_room_id'   => $this->firebase_room_id,
            'room_code'          => $this->room_code,
            'age'                => $this->age,
            'gender'             => $this->gender,
            'weight'             => $this->weight,
            'has_allergies'      => $this->has_allergies === null ? null : (bool) $this->has_allergies,
            'allergy_details'    => $this->allergy_details,
            'marital_status'     => $this->marital_status,
            'functional_status'  => $this->functional_status,
            'race'               => $this->race,
            'education_level'    => $this->education_level,
            'blood_group'        => $this->blood_group,
            'diagnoses'          => $this->whenLoaded('diagnoses', fn () =>
                DiagnosisResource::collection($this->diagnoses)
            ),
            'chronic_diseases'   => $this->whenLoaded('chronicDiseases', fn () =>
                ChronicDiseaseResource::collection($this->chronicDiseases)
            ),
            'attachments'        => $this->whenLoaded('attachments', fn () =>
                RoomAttachmentResource::collection($this->attachments)
            ),
            'active_assignment'  => $this->activeNurseAssignment ? [
                'id'       => $this->activeNurseAssignment->id,
                'template' => $this->activeNurseAssignment->template ? [
                    'id'     => $this->activeNurseAssignment->template->id,
                    'name'   => $this->activeNurseAssignment->template->name,
                    'fields' => $this->activeNurseAssignment->template->fields->map(fn ($field) => [
                        'id'          => $field->id,
                        'question'    => $field->question,
                        'answer_type' => $field->answer_type,
                    ])->values(),
                ] : null,
            ] : null,
            'patient'            => $this->whenLoaded('patient', fn () =>
                new UserResource($this->patient)
            ),
            'members'            => $this->whenLoaded('members', fn () =>
                RoomMemberResource::collection($this->members)
            ),
            'created_at'         => $this->created_at,
        ];
    }
}
