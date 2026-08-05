<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NutritionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'chronic_diseases', 'food_allergies',
        'medicine_allergies', 'current_medications',
        'height', 'weight', 'bmi', 'goal', 'notes', 'status',
    ];

    protected $casts = [
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'bmi'    => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getGoalLabelAttribute(): string
    {
        return match ($this->goal) {
            'lose_weight' => 'انقاص الوزن',
            'gain_weight' => 'زيادة الوزن',
            'maintain'    => 'الحفاظ على الوزن',
            default       => '—',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'بانتظار التأكيد',
            'confirmed'   => 'مؤكد',
            'in_progress' => 'قيد التنفيذ',
            'completed'   => 'مكتمل',
            'cancelled'   => 'ملغي',
            default       => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'warning',
            'confirmed'   => 'info',
            'in_progress' => 'primary',
            'completed'   => 'success',
            'cancelled'   => 'danger',
            default       => 'secondary',
        };
    }
}
