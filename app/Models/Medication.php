<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Medications OUTSIDE rooms — patient fills for themselves only
class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'medication_name', 'dosage', 'route',
        'frequency', 'times', 'start_date', 'end_date', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'times'      => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'user_id');
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
