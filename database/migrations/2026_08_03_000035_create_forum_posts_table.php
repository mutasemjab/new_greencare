<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('forum_sub_category_id')->constrained('forum_sub_categories')->cascadeOnDelete();
            $table->enum('type', ['experience', 'question']); // تجربة أم سؤال
            $table->string('title');
            $table->text('content');
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);   // للإشراف والحذف من الإدارة
            $table->boolean('is_pinned')->default(false);
            $table->unsignedInteger('replies_count')->default(0); // cached count
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_posts');
    }
};
