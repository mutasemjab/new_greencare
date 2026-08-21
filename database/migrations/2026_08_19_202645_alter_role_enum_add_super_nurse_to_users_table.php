<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('doctor','nurse','head_nurse','university_manager','patient','patient_family','super_nurse') NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('doctor','nurse','head_nurse','university_manager','patient','patient_family') NOT NULL");
    }
};
