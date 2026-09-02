<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareService extends Model
{
    use HasFactory, Translatable;

    protected array $translatable = ['name'];

    protected $fillable = ['name', 'name_en', 'icon', 'price', 'is_active', 'sort_order'];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function careRequestServices()
    {
        return $this->hasMany(CareRequestService::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
