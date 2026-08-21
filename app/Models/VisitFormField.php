<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitFormField extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'field_type', 'options', 'sort_order', 'is_active'];

    protected $casts = [
        'options'   => 'array',
        'is_active' => 'boolean',
    ];

    public function answers()
    {
        return $this->hasMany(VisitFormAnswer::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFieldTypeLabelAttribute(): string
    {
        return match ($this->field_type) {
            'text'      => 'نص',
            'number'    => 'رقم',
            'choice'    => 'اختيار واحد',
            'checklist' => 'اختيار متعدد',
            default     => $this->field_type,
        };
    }
}
