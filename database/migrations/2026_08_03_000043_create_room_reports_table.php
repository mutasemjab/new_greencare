<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('report_template_id')->constrained('report_templates'); // which template was used
            // FK to assignment — null for registration reports (filled once at creation)
            $table->foreignId('room_template_assignment_id')
                  ->nullable()
                  ->constrained('room_template_assignments')
                  ->nullOnDelete();
            $table->foreignId('submitted_by')->constrained('users');
            $table->enum('report_type', ['registration', 'nurse', 'doctor']);
            $table->timestamp('submitted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_reports');
    }
};
