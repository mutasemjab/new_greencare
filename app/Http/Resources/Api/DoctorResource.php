<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DoctorResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'photo_url'          => $this->photo ? Storage::url($this->photo) : null,
            'specialty'          => $this->specialty,
            'rating'             => $this->rating !== null ? (float) $this->rating : null,
            'home_visit_price'   => (float) $this->home_visit_price,
            'appointment_price'  => (float) $this->appointment_price,
            'years_experience'   => $this->years_experience,
            'description'        => $this->description,
            'booking_phone'      => $this->booking_phone,
            'is_active'          => (bool) $this->is_active,
        ];
    }
}
