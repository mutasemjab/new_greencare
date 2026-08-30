<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('xray_requests', function (Blueprint $table) {
            $table->string('result_file')->nullable()->after('total');
        });
    }

    public function down()
    {
        Schema::table('xray_requests', function (Blueprint $table) {
            $table->dropColumn('result_file');
        });
    }
};
