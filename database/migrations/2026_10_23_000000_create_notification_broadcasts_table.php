<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A "send to all users" notification queued from the admin dashboard and
     * delivered in the background by `notifications:send-broadcasts`.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_broadcasts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->enum('status', ['pending', 'sending', 'completed', 'failed'])->default('pending');
            $table->unsignedInteger('total_users')->default(0);
            $table->unsignedInteger('processed_count')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            // Resume point: users are processed in id order, so a run that
            // stops half way continues from here on the next cron tick.
            $table->unsignedBigInteger('last_user_id')->default(0);
            $table->foreignId('sent_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_broadcasts');
    }
};
