<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_order_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_order_id')->constrained('doctor_orders')->cascadeOnDelete();
            $table->foreignId('nurse_id')->constrained('users');
            $table->text('reply_text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_order_replies');
    }
};
