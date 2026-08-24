<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'body', 'screen', 'data',
        'type', 'fcm_sent', 'is_read', 'read_at', 'sent_by',
    ];

    protected $casts = [
        'data'     => 'array',
        'fcm_sent' => 'boolean',
        'is_read'  => 'boolean',
        'read_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sentBy()
    {
        return $this->belongsTo(Admin::class, 'sent_by');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
