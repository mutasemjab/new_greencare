<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'forum_sub_category_id', 'type',
        'title', 'content', 'image',
        'is_active', 'is_pinned', 'replies_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_pinned' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(ForumSubCategory::class, 'forum_sub_category_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'experience' => 'تجربة أم',
            'question'   => 'سؤال وجواب',
            default      => $this->type,
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'experience' => 'success',
            'question'   => 'primary',
            default      => 'secondary',
        };
    }
}
