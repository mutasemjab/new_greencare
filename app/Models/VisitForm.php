<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitForm extends Model
{
    use HasFactory;

    protected $fillable = ['patient_id', 'submitted_by'];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function answers()
    {
        return $this->hasMany(VisitFormAnswer::class)->orderBy('sort_order');
    }

    public function attachments()
    {
        return $this->hasMany(VisitFormAttachment::class);
    }
}
