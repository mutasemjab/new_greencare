<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomMedicationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'medication_name' => $this->medication_name,
            'dosage'          => $this->dosage,
            'route'           => $this->route,
            'route_label'     => $this->route_label,
            'frequency'       => $this->frequency,
            'frequency_type'  => $this->frequency_type,
            'times_per_day'   => $this->times_per_day,
            'day_of_week'     => $this->day_of_week,
            'day_of_month'    => $this->day_of_month,
            'times'           => array_values($this->times ?? []),
            'start_date'      => $this->start_date
                ? \Carbon\Carbon::parse($this->start_date)->format('Y-m-d')
                : null,
            'end_date'        => $this->end_date
                ? \Carbon\Carbon::parse($this->end_date)->format('Y-m-d')
                : null,
            'notes'           => $this->notes,
            'added_by'        => $this->whenLoaded('addedBy', fn () => [
                'id'   => $this->addedBy->id,
                'name' => $this->addedBy->name,
            ]),
        ];
    }
}
