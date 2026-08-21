<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorNote extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'doctor_id', 'note'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
