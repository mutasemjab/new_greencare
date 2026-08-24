<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                       => $this->id,
            'name'                     => $this->name,
            'description'              => $this->description,
            'address'                  => $this->address,
            'discount_value'           => $this->discount_value,
            'is_active'                => (bool) $this->is_active,
            'firebase_room_id'         => $this->firebase_room_id,
            'age'                      => $this->age,
            'gender'                   => $this->gender,
            'weight'                   => $this->weight,
            'has_allergies'            => $this->has_allergies === null ? null : (bool) $this->has_allergies,
            'allergy_details'          => $this->allergy_details,
            'marital_status'           => $this->marital_status,
            'functional_status'        => $this->functional_status,
            'race'                     => $this->race,
            'education_level'          => $this->education_level,
            'blood_group'              => $this->blood_group,
            'diagnoses'                => $this->whenLoaded('diagnoses', fn () =>
                DiagnosisResource::collection($this->diagnoses->values())
            ),
            'chronic_diseases'         => $this->whenLoaded('chronicDiseases', fn () =>
                ChronicDiseaseResource::collection($this->chronicDiseases->values())
            ),
            'attachments'              => $this->whenLoaded('attachments', fn () =>
                RoomAttachmentResource::collection($this->attachments->values())
            ),
            'active_assignment'        => $this->assignmentToArray($this->activeNurseAssignment),
            'doctor_active_assignment' => $this->assignmentToArray($this->activeDoctorAssignment),
            'patient'                  => $this->whenLoaded('patient', fn () =>
                new UserResource($this->patient)
            ),
            'members'                  => $this->whenLoaded('members', fn () =>
                RoomMemberResource::collection($this->members->values())
            ),
            'created_at'               => $this->created_at,
        ];
    }

    private function assignmentToArray($assignment): ?array
    {
        if (! $assignment) {
            return null;
        }

        return [
            'id'       => $assignment->id,
            'template' => $assignment->template ? [
                'id'     => $assignment->template->id,
                'name'   => $assignment->template->name,
                'fields' => $assignment->template->fields->map(fn ($field) => [
                    'id'          => $field->id,
                    'question'    => $field->question,
                    'answer_type' => $field->answer_type,
                ])->values(),
            ] : null,
        ];
    }
}
