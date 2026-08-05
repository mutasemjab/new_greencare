<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'photo', 'specialty',
        'home_visit_price', 'appointment_price',
        'rating', 'years_experience', 'description',
        'booking_phone', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'home_visit_price'  => 'decimal:2',
        'appointment_price' => 'decimal:2',
        'rating'            => 'decimal:1',
        'is_active'         => 'boolean',
    ];

    public function bookings()
    {
        return $this->hasMany(DoctorBooking::class);
    }

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
