<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->enum('visit_type', ['home_visit', 'appointment'])->default('home_visit');
            $table->foreignId('address_id')->nullable()->constrained('user_addresses')->nullOnDelete();
            $table->date('booking_date');
            $table->time('booking_time');
            $table->text('notes')->nullable();
            $table->decimal('price', 10, 2)->default(0); // snapshot of price at booking time
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_bookings');
    }
};
