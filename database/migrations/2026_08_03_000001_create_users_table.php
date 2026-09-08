<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20)->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->enum('role', ['doctor', 'nurse', 'university_manager', 'patient', 'patient_family', 'super_nurse']);
            $table->string('fcm_token')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('patient_code', 20)->unique()->nullable(); // auto-generated for patients
            $table->foreignId('related_patient_id')->nullable()->constrained('users')->nullOnDelete(); // for patient_family
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
