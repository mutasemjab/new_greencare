<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bathing_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('patient_code', 20);
            $table->enum('payment_type', ['card', 'cash']);
            $table->foreignId('bathing_card_id')->nullable()->constrained('bathing_cards')->nullOnDelete();
            $table->foreignId('point_of_sale_id')->nullable()->constrained('points_of_sale')->nullOnDelete();
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
        Schema::dropIfExists('bathing_requests');
    }
};
