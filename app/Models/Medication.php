<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Medications OUTSIDE rooms — patient fills for themselves only
class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'medication_name', 'dosage',
        'frequency', 'start_date', 'end_date', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
