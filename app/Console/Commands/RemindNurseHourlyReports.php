<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\FCMController;
use App\Models\Room;
use App\Models\RoomReport;
use Illuminate\Console\Command;

class RemindNurseHourlyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sihati:remind-nurses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminds each nurse to fill the hourly report, per room they are a member of, if not already filled this hour';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $currentHour = now('Asia/Amman')->format('H:00');

        $rooms = Room::where('is_active', true)
            ->whereHas('activeNurseAssignment')
            ->with(['members' => fn ($q) => $q->where('role', 'nurse')])
            ->get();

        $sent = 0;

        foreach ($rooms as $room) {
            $alreadyFilled = RoomReport::where('room_id', $room->id)
                ->where('report_hour', $currentHour)
                ->exists();

            if ($alreadyFilled || $room->members->isEmpty()) {
                continue;
            }

            foreach ($room->members as $member) {
                FCMController::sendToUser(
                    $member->user_id,
                    'تذكير بالتقرير الساعي',
                    "حان وقت تعبئة التقرير الساعي لغرفة \"{$room->name}\"",
                    'room_report',
                    'nurse_report_reminder'
                );
                $sent++;
            }
        }

        $this->info("Nurse hourly report reminders sent: {$sent}");

        return Command::SUCCESS;
    }
}
