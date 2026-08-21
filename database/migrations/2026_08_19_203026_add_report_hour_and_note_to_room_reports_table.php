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
        Schema::table('room_reports', function (Blueprint $table) {
            $table->string('report_hour', 5)->nullable()->after('report_type');
            $table->text('note')->nullable()->after('report_hour');
            $table->unique(['room_id', 'report_hour']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('room_reports', function (Blueprint $table) {
            $table->dropUnique(['room_id', 'report_hour']);
            $table->dropColumn(['report_hour', 'note']);
        });
    }
};
