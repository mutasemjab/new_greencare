<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiClient extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'key_hash', 'is_active', 'last_used_at'];

    protected $casts = [
        'is_active'    => 'boolean',
        'last_used_at' => 'datetime',
    ];

    /**
     * Generates a new raw API key and its hash. The raw key is returned
     * only here, to be shown to the admin once — only the hash is ever
     * persisted, so the raw key can never be recovered later.
     */
    public static function generateKey(): array
    {
        $rawKey = 'gc_' . Str::random(40);

        return [
            'raw'  => $rawKey,
            'hash' => hash('sha256', $rawKey),
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
