<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory, Translatable;

    protected array $translatable = ['name', 'specialty', 'description'];

    protected $fillable = [
        'name', 'name_en', 'photo', 'specialty', 'specialty_en',
        'home_visit_price', 'appointment_price',
        'rating', 'years_experience', 'description', 'description_en',
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
