<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('room_medications', function (Blueprint $table) {
            $table->enum('route', ['oral', 'iv', 'im', 'subcutaneous', 'topical', 'inhalation', 'other'])
                ->nullable()->after('dosage');
        });

        Schema::table('medications', function (Blueprint $table) {
            $table->enum('route', ['oral', 'iv', 'im', 'subcutaneous', 'topical', 'inhalation', 'other'])
                ->nullable()->after('dosage');
        });
    }

    public function down()
    {
        Schema::table('room_medications', function (Blueprint $table) {
            $table->dropColumn('route');
        });

        Schema::table('medications', function (Blueprint $table) {
            $table->dropColumn('route');
        });
    }
};
