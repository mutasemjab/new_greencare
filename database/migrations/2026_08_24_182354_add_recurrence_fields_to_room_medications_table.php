<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('room_medications', function (Blueprint $table) {
            $table->enum('frequency_type', ['daily', 'weekly', 'monthly'])->nullable()->after('frequency');
            $table->unsignedTinyInteger('times_per_day')->nullable()->after('frequency_type');
            $table->unsignedTinyInteger('day_of_week')->nullable()->after('times_per_day');
            $table->unsignedTinyInteger('day_of_month')->nullable()->after('day_of_week');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('room_medications', function (Blueprint $table) {
            $table->dropColumn(['frequency_type', 'times_per_day', 'day_of_week', 'day_of_month']);
        });
    }
};
