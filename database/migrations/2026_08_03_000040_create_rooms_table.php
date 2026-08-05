<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users');              // المريض
            $table->foreignId('created_by')->constrained('users');              // رئيس الممرضين
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->decimal('discount_value', 5, 2)->default(0);                // نسبة الخصم %
            // قالب التسجيل المختار عند إنشاء الغرفة (registration type only)
            $table->foreignId('registration_template_id')
                  ->nullable()
                  ->constrained('report_templates')
                  ->nullOnDelete();
            $table->string('firebase_room_id')->nullable();                     // للتكامل مع Firebase
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
