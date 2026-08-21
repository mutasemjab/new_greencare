<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_diagnosis');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
