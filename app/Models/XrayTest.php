<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XrayTest extends Model
{
    use HasFactory;

    protected $fillable = ['xray_category_id', 'name', 'description', 'price', 'is_active'];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(XrayCategory::class, 'xray_category_id');
    }

    public function requestTests()
    {
        return $this->hasMany(XrayRequestTest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
