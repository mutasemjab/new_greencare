<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class LabStaff extends Authenticatable
{
    use HasFactory;

    protected $table = 'lab_staff';

    protected $fillable = ['name', 'phone', 'password', 'is_active'];

    protected $hidden = ['password'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
