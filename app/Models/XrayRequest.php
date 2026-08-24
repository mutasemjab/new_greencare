<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XrayRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'patient_code', 'address_id', 'room_id',
        'booking_date', 'booking_time', 'notes', 'total', 'status',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'total'        => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'address_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function tests()
    {
        return $this->hasMany(XrayRequestTest::class);
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
