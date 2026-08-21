<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'patient_id', 'submitted_by', 'complaint_text', 'status'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'قيد المراجعة',
            'reviewed' => 'تمت المراجعة',
            default    => $this->status,
        };
    }
}
