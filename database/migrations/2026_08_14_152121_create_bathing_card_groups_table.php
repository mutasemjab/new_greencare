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
        Schema::create('bathing_card_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->foreignId('sold_at_point_id')->nullable()->constrained('points_of_sale')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bathing_card_groups');
    }
};
