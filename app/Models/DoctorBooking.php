<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'doctor_id', 'visit_type',
        'address_id', 'booking_date', 'booking_time',
        'notes', 'price', 'status',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'price'        => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'address_id');
    }

    public function getVisitTypeLabelAttribute(): string
    {
        return match ($this->visit_type) {
            'home_visit'  => 'زيارة منزلية',
            'appointment' => 'موعد عيادة',
            default       => $this->visit_type,
        };
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
