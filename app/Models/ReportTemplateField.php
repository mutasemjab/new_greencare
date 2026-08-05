<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportTemplateField extends Model
{
    protected $fillable = [
        'report_template_id', 'question',
        'answer_type', 'is_required', 'sort_order',
    ];

    protected $casts = ['is_required' => 'boolean'];

    public function template()
    {
        return $this->belongsTo(ReportTemplate::class, 'report_template_id');
    }

    public function answers()
    {
        return $this->hasMany(RoomReportAnswer::class, 'report_template_field_id');
    }

    public function getAnswerTypeLabelAttribute(): string
    {
        return match ($this->answer_type) {
            'text'    => 'نص',
            'number'  => 'رقم',
            'yes_no'  => 'نعم / لا',
            'image'   => 'صورة',
            default   => $this->answer_type,
        };
    }
}
