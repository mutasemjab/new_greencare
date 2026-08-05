<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NursingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'patient_code', 'nursing_service_type_id',
        'address_id', 'booking_date', 'booking_time', 'notes', 'status',
    ];

    protected $casts = ['booking_date' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(NursingServiceType::class, 'nursing_service_type_id');
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'address_id');
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
