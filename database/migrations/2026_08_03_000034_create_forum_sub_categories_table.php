<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_category_id')->constrained('forum_categories')->cascadeOnDelete();
            $table->string('name');           // مثل: رعاية الأطفال، التغذية، الصحة العامة
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_sub_categories');
    }
};
