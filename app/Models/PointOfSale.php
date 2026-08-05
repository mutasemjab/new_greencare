<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointOfSale extends Model
{
    use HasFactory;

    protected $table = 'points_of_sale';

    protected $fillable = ['name', 'address', 'phone', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function bathingCards()
    {
        return $this->hasMany(BathingCard::class, 'sold_at_point_id');
    }

    public function bathingRequests()
    {
        return $this->hasMany(BathingRequest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
