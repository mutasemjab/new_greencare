<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'phone'         => $this->phone,
            'email'         => $this->email,
            'role'          => $this->role,
            'gender'        => $this->gender,
            'date_of_birth' => $this->date_of_birth
                ? \Carbon\Carbon::parse($this->date_of_birth)->format('Y-m-d')
                : null,
            'patient_code'  => $this->patient_code,
            'is_active'     => $this->is_active,
            'fcm_token'     => $this->fcm_token,
            'created_at'    => $this->created_at,
        ];
    }
}
