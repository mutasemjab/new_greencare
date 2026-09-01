<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('xray_request_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('xray_request_id')->constrained('xray_requests')->cascadeOnDelete();
            $table->foreignId('xray_test_id')->constrained('xray_tests');
            $table->decimal('unit_price', 10, 2); // price snapshot at booking time
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xray_request_tests');
    }
};
