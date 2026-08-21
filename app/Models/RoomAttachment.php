<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RoomAttachment extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'uploaded_by', 'file_path', 'original_name'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }
}
