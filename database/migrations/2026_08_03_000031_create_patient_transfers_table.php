<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('from_zone_id')->constrained('delivery_zones'); // منطقة الانطلاق
            $table->foreignId('to_zone_id')->constrained('delivery_zones');   // منطقة الوصول
            $table->string('from_location');               // وصف نقطة الانطلاق
            $table->string('to_location');                 // وصف نقطة الوصول
            $table->decimal('from_latitude', 10, 7)->nullable();
            $table->decimal('from_longitude', 10, 7)->nullable();
            $table->decimal('to_latitude', 10, 7)->nullable();
            $table->decimal('to_longitude', 10, 7)->nullable();
            $table->date('booking_date');
            $table->time('booking_time');
            $table->text('case_description')->nullable();  // وصف الحالة
            $table->text('notes')->nullable();
            $table->decimal('price', 10, 2)->default(0);  // السعر المحسوب
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_transfers');
    }
};
