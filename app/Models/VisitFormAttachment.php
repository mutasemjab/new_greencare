<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VisitFormAttachment extends Model
{
    protected $fillable = ['visit_form_id', 'file_path'];

    public function visitForm()
    {
        return $this->belongsTo(VisitForm::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }
}
