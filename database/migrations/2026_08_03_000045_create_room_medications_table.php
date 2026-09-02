<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('added_by')->constrained('users'); // doctor or patient
            $table->string('medication_name');
            $table->string('dosage')->nullable();
            $table->enum('route', ['oral', 'iv', 'im', 'subcutaneous', 'topical', 'inhalation', 'other'])->nullable();
            $table->string('frequency')->nullable();
            $table->json('times')->nullable();
            $table->enum('frequency_type', ['daily', 'weekly', 'monthly'])->nullable();
            $table->unsignedTinyInteger('times_per_day')->nullable();
            $table->unsignedTinyInteger('day_of_week')->nullable();
            $table->unsignedTinyInteger('day_of_month')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_medications');
    }
};
