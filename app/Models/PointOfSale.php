<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointOfSale extends Model
{
    use HasFactory, Translatable;

    protected array $translatable = ['name'];

    protected $table = 'points_of_sale';

    protected $fillable = ['name', 'name_en', 'address', 'phone', 'is_active'];

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
