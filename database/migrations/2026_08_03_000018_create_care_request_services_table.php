<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_request_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('care_request_id')->constrained('care_requests')->cascadeOnDelete();
            $table->foreignId('care_service_id')->constrained('care_services');
            $table->decimal('unit_price', 10, 2); // price snapshot at booking time
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_request_services');
    }
};
