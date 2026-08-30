<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lab_requests', function (Blueprint $table) {
            $table->foreignId('visit_form_id')->nullable()->after('room_id')
                ->constrained('visit_forms')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('lab_requests', function (Blueprint $table) {
            $table->dropForeign(['visit_form_id']);
            $table->dropColumn('visit_form_id');
        });
    }
};
