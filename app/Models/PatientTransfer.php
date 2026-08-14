<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'from_location', 'to_location',
        'from_latitude', 'from_longitude',
        'to_latitude', 'to_longitude',
        'booking_date', 'booking_time',
        'case_description', 'notes', 'price', 'status',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'price'        => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'بانتظار التأكيد',
            'confirmed'   => 'مؤكد',
            'in_progress' => 'قيد التنفيذ',
            'completed'   => 'مكتمل',
            'cancelled'   => 'ملغي',
            default       => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'warning',
            'confirmed'   => 'info',
            'in_progress' => 'primary',
            'completed'   => 'success',
            'cancelled'   => 'danger',
            default       => 'secondary',
        };
    }
}
