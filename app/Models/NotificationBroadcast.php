<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationBroadcast extends Model
{
    protected $fillable = [
        'title', 'body', 'status', 'total_users', 'processed_count', 'sent_count',
        'failed_count', 'last_user_id', 'sent_by', 'started_at', 'finished_at', 'error',
    ];

    protected $casts = [
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function sentBy()
    {
        return $this->belongsTo(Admin::class, 'sent_by');
    }

    public function getProgressPercentAttribute(): int
    {
        if ($this->total_users <= 0) {
            return $this->status === 'completed' ? 100 : 0;
        }

        return (int) min(100, floor($this->processed_count * 100 / $this->total_users));
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'بانتظار الإرسال',
            'sending'   => 'جاري الإرسال',
            'completed' => 'تم الإرسال',
            'failed'    => 'فشل',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'secondary',
            'sending'   => 'primary',
            'completed' => 'success',
            'failed'    => 'danger',
            default     => 'secondary',
        };
    }
}
