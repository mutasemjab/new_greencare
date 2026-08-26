<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bathing_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->unsignedTinyInteger('max_uses')->default(5); // configurable per card
            $table->unsignedTinyInteger('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('sold_at_point_id')->nullable()->constrained('points_of_sale')->nullOnDelete();
            $table->foreignId('bathing_card_group_id')->nullable()
                ->constrained('bathing_card_groups')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bathing_cards');
    }
};
