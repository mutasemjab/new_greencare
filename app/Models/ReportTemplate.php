<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'template_type', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function fields()
    {
        return $this->hasMany(ReportTemplateField::class)->orderBy('sort_order');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'registration_template_id');
    }

    public function assignments()
    {
        return $this->hasMany(RoomTemplateAssignment::class);
    }

    public function reports()
    {
        return $this->hasMany(RoomReport::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->template_type) {
            'registration' => 'قالب التسجيل',
            'nurse'        => 'تقرير الممرض (كل ساعة)',
            'doctor'       => 'تقرير الدكتور (كل شهر)',
            default        => $this->template_type,
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->template_type) {
            'registration' => 'secondary',
            'nurse'        => 'primary',
            'doctor'       => 'success',
            default        => 'light',
        };
    }

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $q): \Illuminate\Database\Eloquent\Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeOfType(\Illuminate\Database\Eloquent\Builder $q, string $type): \Illuminate\Database\Eloquent\Builder
    {
        return $q->where('template_type', $type);
    }
}
