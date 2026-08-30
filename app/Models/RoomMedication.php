<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomMedication extends Model
{
    protected $fillable = [
        'room_id', 'added_by', 'medication_name',
        'dosage', 'route', 'frequency', 'frequency_type', 'times_per_day', 'day_of_week', 'day_of_month',
        'times', 'start_date', 'end_date', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'times'      => 'array',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function getRouteLabelAttribute(): ?string
    {
        return match ($this->route) {
            'oral'         => 'عن طريق الفم',
            'iv'           => 'عن طريق الوريد',
            'im'           => 'عن طريق العضل',
            'subcutaneous' => 'تحت الجلد',
            'topical'      => 'موضعي',
            'inhalation'   => 'استنشاق',
            'other'        => 'أخرى',
            default        => null,
        };
    }
}
