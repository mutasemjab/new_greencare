<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_template_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('report_template_id')->constrained('report_templates');
            $table->foreignId('assigned_by')->constrained('admins');
            $table->timestamp('assigned_at');
            // null = currently active; set when template is replaced
            $table->timestamp('unassigned_at')->nullable();
            $table->timestamps();

            // only one active assignment per room (unassigned_at IS NULL per room)
            // enforced at application level, not DB constraint
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_template_assignments');
    }
};
