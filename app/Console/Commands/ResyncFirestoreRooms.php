<?php

namespace App\Console\Commands;

use App\Models\Room;
use App\Services\FirebaseService;
use Illuminate\Console\Command;

class ResyncFirestoreRooms extends Command
{
    protected $signature = 'sihati:resync-firestore';

    protected $description = 'Re-syncs all active rooms members array in Firestore to ensure correct data types';

    public function handle(FirebaseService $firebase): int
    {
        $rooms = Room::whereNotNull('firebase_room_id')->get();

        $bar = $this->output->createProgressBar($rooms->count());
        $bar->start();

        $synced = 0;
        $failed = 0;

        foreach ($rooms as $room) {
            try {
                $firebase->syncRoomMembers($room);
                $synced++;
            } catch (\Throwable $e) {
                $failed++;
                $this->newLine();
                $this->error("Room {$room->id} failed: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done — synced: {$synced}, failed: {$failed}");

        return Command::SUCCESS;
    }
}
