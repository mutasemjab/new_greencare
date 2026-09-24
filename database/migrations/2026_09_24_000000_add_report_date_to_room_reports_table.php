<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_reports', function (Blueprint $table) {
            $table->date('report_date')->nullable()->after('report_hour');
        });

        // Backfill report_date for existing hourly reports from submitted_at,
        // since report_hour only ever stored "H:00" with no date component.
        DB::table('room_reports')
            ->whereNotNull('report_hour')
            ->orderBy('id')
            ->select('id', 'submitted_at')
            ->get()
            ->each(function ($row) {
                DB::table('room_reports')->where('id', $row->id)->update([
                    'report_date' => \Carbon\Carbon::parse($row->submitted_at)
                        ->timezone('Asia/Amman')
                        ->toDateString(),
                ]);
            });

        Schema::table('room_reports', function (Blueprint $table) {
            // Old constraint let a room fill a given hour (e.g. "12:00") only
            // once ever, blocking every future day at that same hour.
            $table->dropUnique(['room_id', 'report_hour']);
            $table->unique(['room_id', 'report_hour', 'report_date']);
        });
    }

    public function down(): void
    {
        Schema::table('room_reports', function (Blueprint $table) {
            $table->dropUnique(['room_id', 'report_hour', 'report_date']);
            $table->unique(['room_id', 'report_hour']);
            $table->dropColumn('report_date');
        });
    }
};
