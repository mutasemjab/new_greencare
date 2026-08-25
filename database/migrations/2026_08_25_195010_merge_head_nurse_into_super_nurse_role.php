<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * head_nurse and super_nurse were the same role under two names.
     * Merge every existing head_nurse user into super_nurse, then drop
     * head_nurse from the enum entirely so it can't be picked again.
     */
    public function up()
    {
        DB::table('users')->where('role', 'head_nurse')->update(['role' => 'super_nurse']);

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('doctor','nurse','university_manager','patient','patient_family','super_nurse') NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('doctor','nurse','head_nurse','university_manager','patient','patient_family','super_nurse') NOT NULL");
    }
};
