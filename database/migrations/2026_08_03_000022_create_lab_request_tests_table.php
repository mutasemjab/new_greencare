<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_request_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_request_id')->constrained('lab_requests')->cascadeOnDelete();
            $table->foreignId('lab_test_id')->constrained('lab_tests');
            $table->decimal('unit_price', 10, 2); // price snapshot at booking time
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_request_tests');
    }
};
