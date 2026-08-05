<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nursing_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('patient_code', 20); // stored as string, not FK
            $table->foreignId('nursing_service_type_id')->constrained('nursing_service_types');
            $table->foreignId('address_id')->nullable()->constrained('user_addresses')->nullOnDelete();
            $table->date('booking_date');
            $table->time('booking_time');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nursing_requests');
    }
};
