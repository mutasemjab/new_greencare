<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    protected $fillable = ['type', 'title', 'content', 'updated_by'];

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'authorization' => 'تقرير التفويض',
            'pledge'        => 'تقرير التعهد',
            default         => $this->type,
        };
    }

    // Helper: get or create the singleton record for each type
    public static function forType(string $type): self
    {
        return self::firstOrCreate(
            ['type' => $type],
            ['title' => $type === 'authorization' ? 'تقرير التفويض' : 'تقرير التعهد', 'content' => '']
        );
    }
}
