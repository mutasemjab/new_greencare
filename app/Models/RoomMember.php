<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomMember extends Model
{
    protected $fillable = ['room_id', 'user_id', 'role'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'doctor'         => 'طبيب',
            'nurse'          => 'ممرض',
            'patient_family' => 'عيلة المريض',
            'super_nurse'    => 'ممرض مسؤول',
            default          => $this->role,
        };
    }
}
