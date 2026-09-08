<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChronicDisease extends Model
{
    use HasFactory, Translatable;

    protected array $translatable = ['name'];

    protected $fillable = ['name', 'name_en', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_chronic_disease');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
