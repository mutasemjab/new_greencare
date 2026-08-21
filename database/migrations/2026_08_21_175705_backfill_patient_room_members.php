<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rooms created before room_members.role gained 'patient' never got a
     * membership row for their own patient — only an implicit link via
     * rooms.patient_id. This backfills that row so the API's `members`
     * list matches what's already synced into Firestore (which computes
     * membership from patient_id + created_by + room_members directly).
     */
    public function up()
    {
        $rooms = DB::table('rooms')->whereNotNull('patient_id')->get(['id', 'patient_id']);

        foreach ($rooms as $room) {
            $exists = DB::table('room_members')
                ->where('room_id', $room->id)
                ->where('user_id', $room->patient_id)
                ->exists();

            if (! $exists) {
                DB::table('room_members')->insert([
                    'room_id'    => $room->id,
                    'user_id'    => $room->patient_id,
                    'role'       => 'patient',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
        DB::table('room_members')->where('role', 'patient')->delete();
    }
};
