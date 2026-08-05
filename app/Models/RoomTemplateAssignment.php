<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomTemplateAssignment extends Model
{
    protected $fillable = [
        'room_id', 'report_template_id',
        'assigned_by', 'assigned_at', 'unassigned_at',
    ];

    protected $casts = [
        'assigned_at'   => 'datetime',
        'unassigned_at' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function template()
    {
        return $this->belongsTo(ReportTemplate::class, 'report_template_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(Admin::class, 'assigned_by');
    }

    public function reports()
    {
        return $this->hasMany(RoomReport::class);
    }

    public function getIsActiveAttribute(): bool
    {
        return is_null($this->unassigned_at);
    }
}
