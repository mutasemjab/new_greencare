<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_report_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_report_id')->constrained('room_reports')->cascadeOnDelete();
            // nullable: field may be deleted later, but answer is preserved via snapshots
            $table->foreignId('report_template_field_id')
                  ->nullable()
                  ->constrained('report_template_fields')
                  ->nullOnDelete();

            // ── Snapshots (never change even if template field is edited/deleted) ──
            $table->string('field_question');               // snapshot of question text
            $table->enum('field_answer_type', ['text', 'number', 'yes_no', 'image']); // snapshot
            $table->unsignedSmallInteger('sort_order')->default(0);

            // ── Answer columns (only one will be filled per answer_type) ──
            $table->text('answer_text')->nullable();
            $table->decimal('answer_number', 15, 4)->nullable();
            $table->boolean('answer_boolean')->nullable();
            $table->string('answer_image')->nullable();     // file path

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_report_answers');
    }
};
