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
            $table->foreignId('patient_id')->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->text('description_en')->nullable();
            $table->string('address')->nullable();
            $table->decimal('discount_value', 5, 2)->default(0);
            $table->foreignId('registration_template_id')
                  ->nullable()
                  ->constrained('report_templates')
                  ->nullOnDelete();
            $table->string('firebase_room_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('patient_code', 20)->unique()->nullable();
            $table->unsignedSmallInteger('age')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->boolean('has_allergies')->nullable();
            $table->text('allergy_details')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->enum('functional_status', ['independent', 'partially_dependent', 'fully_dependent'])->nullable();
            $table->enum('race', ['white', 'black'])->nullable();
            $table->string('education_level')->nullable();
            $table->string('blood_group', 3)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
