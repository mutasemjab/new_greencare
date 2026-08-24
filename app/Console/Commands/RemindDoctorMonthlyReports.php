<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\FCMController;
use App\Models\Room;
use App\Models\RoomReport;
use Illuminate\Console\Command;

class RemindDoctorMonthlyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sihati:remind-doctors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminds each doctor to fill the monthly report, per room they are a member of, if not already filled this month';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $currentMonth = now('Asia/Amman')->format('Y-m');

        $rooms = Room::where('is_active', true)
            ->whereHas('activeDoctorAssignment')
            ->with(['members' => fn ($q) => $q->where('role', 'doctor')])
            ->get();

        $sent = 0;

        foreach ($rooms as $room) {
            $alreadyFilled = RoomReport::where('room_id', $room->id)
                ->where('report_month', $currentMonth)
                ->exists();

            if ($alreadyFilled || $room->members->isEmpty()) {
                continue;
            }

            foreach ($room->members as $member) {
                FCMController::sendToUser(
                    $member->user_id,
                    'تذكير بالتقرير الشهري',
                    "حان وقت تعبئة التقرير الشهري لغرفة \"{$room->name}\"",
                    'room_report',
                    'doctor_report_reminder'
                );
                $sent++;
            }
        }

        $this->info("Doctor monthly report reminders sent: {$sent}");

        return Command::SUCCESS;
    }
}
