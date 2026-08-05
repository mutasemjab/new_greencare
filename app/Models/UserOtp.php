<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOtp extends Model
{
    protected $fillable = ['phone', 'otp', 'expires_at', 'used_at'];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];

    /**
     * Scope: OTPs that are not expired and not yet used.
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now())
                     ->whereNull('used_at');
    }
}
