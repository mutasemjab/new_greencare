<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumReply extends Model
{
    use HasFactory;

    protected $fillable = ['forum_post_id', 'user_id', 'content', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'forum_post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        // sync replies_count on parent post
        static::created(function (ForumReply $reply) {
            $reply->post->increment('replies_count');
        });

        static::deleted(function (ForumReply $reply) {
            $reply->post->decrement('replies_count');
        });
    }
}
