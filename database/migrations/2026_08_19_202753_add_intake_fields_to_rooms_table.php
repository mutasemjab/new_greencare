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
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('room_code', 20)->nullable()->unique()->after('patient_code');
            $table->unsignedSmallInteger('age')->nullable()->after('room_code');
            $table->enum('gender', ['male', 'female'])->nullable()->after('age');
            $table->decimal('weight', 5, 2)->nullable()->after('gender');
            $table->boolean('has_allergies')->nullable()->after('weight');
            $table->text('allergy_details')->nullable()->after('has_allergies');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->after('allergy_details');
            $table->enum('functional_status', ['independent', 'partially_dependent', 'fully_dependent'])->nullable()->after('marital_status');
            $table->enum('race', ['white', 'black'])->nullable()->after('functional_status');
            $table->string('education_level')->nullable()->after('race');
            $table->string('blood_group', 3)->nullable()->after('education_level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn([
                'room_code', 'age', 'gender', 'weight', 'has_allergies', 'allergy_details',
                'marital_status', 'functional_status', 'race', 'education_level', 'blood_group',
            ]);
        });
    }
};
