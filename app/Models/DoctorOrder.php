<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorOrder extends Model
{
    protected $fillable = [
        'room_id', 'doctor_id', 'order_text',
        'is_executed', 'executed_at',
    ];

    protected $casts = [
        'is_executed' => 'boolean',
        'executed_at' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function replies()
    {
        return $this->hasMany(DoctorOrderReply::class)->latest();
    }
}
