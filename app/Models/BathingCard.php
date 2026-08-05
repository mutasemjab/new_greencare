<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BathingCard extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'max_uses', 'used_count', 'is_active', 'sold_at_point_id'];

    protected $casts = ['is_active' => 'boolean'];

    public function pointOfSale()
    {
        return $this->belongsTo(PointOfSale::class, 'sold_at_point_id');
    }

    public function requests()
    {
        return $this->hasMany(BathingRequest::class);
    }

    public function getRemainingUsesAttribute(): int
    {
        return max(0, $this->max_uses - $this->used_count);
    }

    public function getIsExhaustedAttribute(): bool
    {
        return $this->used_count >= $this->max_uses;
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)->whereColumn('used_count', '<', 'max_uses');
    }
}
