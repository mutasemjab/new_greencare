<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomReportAnswer extends Model
{
    protected $fillable = [
        'room_report_id', 'report_template_field_id',
        'field_question', 'field_answer_type', 'sort_order',
        'answer_text', 'answer_number', 'answer_boolean', 'answer_image',
    ];

    protected $casts = [
        'answer_boolean' => 'boolean',
        'answer_number'  => 'decimal:4',
    ];

    public function report()
    {
        return $this->belongsTo(RoomReport::class, 'room_report_id');
    }

    public function templateField()
    {
        return $this->belongsTo(ReportTemplateField::class, 'report_template_field_id');
    }

    // Returns the human-readable answer regardless of type
    public function getDisplayAnswerAttribute(): string
    {
        return match ($this->field_answer_type) {
            'text'   => $this->answer_text ?? '—',
            'number' => $this->answer_number !== null ? (string) (float) $this->answer_number : '—',
            'yes_no' => match ($this->answer_boolean) {
                true  => 'نعم',
                false => 'لا',
                null  => '—',
            },
            'image'  => $this->answer_image ? 'صورة مرفقة' : '—',
            default  => '—',
        };
    }
}
