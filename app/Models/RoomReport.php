<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomReport extends Model
{
    protected $fillable = [
        'room_id', 'report_template_id', 'room_template_assignment_id',
        'submitted_by', 'report_type', 'submitted_at', 'report_hour', 'report_month', 'note',
    ];

    protected $casts = ['submitted_at' => 'datetime'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function template()
    {
        return $this->belongsTo(ReportTemplate::class, 'report_template_id');
    }

    public function assignment()
    {
        return $this->belongsTo(RoomTemplateAssignment::class, 'room_template_assignment_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function answers()
    {
        return $this->hasMany(RoomReportAnswer::class)->orderBy('sort_order');
    }

    public function getReportTypeLabelAttribute(): string
    {
        return match ($this->report_type) {
            'registration' => 'تقرير التسجيل',
            'nurse'        => 'تقرير ممرض',
            'doctor'       => 'تقرير دكتور',
            default        => $this->report_type,
        };
    }

    public function getReportTypeColorAttribute(): string
    {
        return match ($this->report_type) {
            'registration' => 'secondary',
            'nurse'        => 'primary',
            'doctor'       => 'success',
            default        => 'light',
        };
    }
}
