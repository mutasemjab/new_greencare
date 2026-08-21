<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitFormAnswer extends Model
{
    protected $fillable = [
        'visit_form_id', 'visit_form_field_id',
        'field_question', 'field_type', 'sort_order',
        'answer_text', 'answer_json',
    ];

    protected $casts = [
        'answer_json' => 'array',
    ];

    public function visitForm()
    {
        return $this->belongsTo(VisitForm::class);
    }

    public function field()
    {
        return $this->belongsTo(VisitFormField::class, 'visit_form_field_id');
    }

    // Returns the human-readable answer regardless of type
    public function getDisplayAnswerAttribute(): string
    {
        return match ($this->field_type) {
            'text', 'number', 'choice' => $this->answer_text ?? '—',
            'checklist' => $this->answer_json ? implode('، ', $this->answer_json) : '—',
            default     => '—',
        };
    }
}
