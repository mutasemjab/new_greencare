<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo')->nullable();
            $table->string('specialty');
            $table->decimal('home_visit_price', 10, 2)->default(0);
            $table->decimal('appointment_price', 10, 2)->default(0);
            $table->decimal('rating', 2, 1)->default(0); // 1.0 to 5.0, set by admin
            $table->unsignedTinyInteger('years_experience')->default(0);
            $table->text('description')->nullable();
            $table->string('booking_phone', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
