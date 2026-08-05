<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nutrition_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('chronic_diseases')->nullable();   // أمراض مزمنة: سكري، ضغط، إلخ
            $table->text('food_allergies')->nullable();     // حساسية من أطعمة
            $table->text('medicine_allergies')->nullable(); // حساسية من أدوية
            $table->text('current_medications')->nullable();// الأدوية الحالية
            $table->decimal('height', 5, 2)->nullable();   // الطول بالسم
            $table->decimal('weight', 5, 2)->nullable();   // الوزن بالكغ
            $table->decimal('bmi', 5, 2)->nullable();      // مؤشر كتلة الجسم (محسوب)
            $table->enum('goal', ['lose_weight', 'gain_weight', 'maintain'])->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutrition_requests');
    }
};
