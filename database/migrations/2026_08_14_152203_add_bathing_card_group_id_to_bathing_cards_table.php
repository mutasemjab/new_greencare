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
        Schema::table('bathing_cards', function (Blueprint $table) {
            $table->foreignId('bathing_card_group_id')->nullable()->after('id')
                ->constrained('bathing_card_groups')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bathing_cards', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bathing_card_group_id');
        });
    }
};
