<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('visit_forms', function (Blueprint $table) {
            $table->string('code', 20)->nullable()->unique()->after('id');
            $table->decimal('discount_value', 5, 2)->default(0)->after('code');
        });
    }

    public function down()
    {
        Schema::table('visit_forms', function (Blueprint $table) {
            $table->dropColumn(['code', 'discount_value']);
        });
    }
};
