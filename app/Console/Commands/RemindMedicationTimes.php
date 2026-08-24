<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\FCMController;
use App\Models\RoomMedication;
use Illuminate\Console\Command;

class RemindMedicationTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sihati:remind-medications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends an FCM reminder to every room member when a medication dose is due';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = now('Asia/Amman');
        $currentTime = $now->format('H:i');
        $today = $now->toDateString();

        $medications = RoomMedication::whereNotNull('frequency_type')
            ->where('start_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->with('room')
            ->get();

        $sent = 0;

        foreach ($medications as $medication) {
            $room = $medication->room;

            if (! $room || ! $room->is_active) {
                continue;
            }

            $times = $medication->times ?? [];

            if (! in_array($currentTime, $times, true)) {
                continue;
            }

            $isDueToday = match ($medication->frequency_type) {
                'daily'   => true,
                'weekly'  => $medication->day_of_week === $now->dayOfWeek,
                'monthly' => $medication->day_of_month === (int) $now->format('j'),
                default   => false,
            };

            if (! $isDueToday) {
                continue;
            }

            foreach ($room->memberUserIds() as $userId) {
                FCMController::sendToUser(
                    $userId,
                    'تذكير بموعد دواء',
                    "حان موعد دواء \"{$medication->medication_name}\" ({$medication->dosage}) — غرفة {$room->name}",
                    'medication',
                    'medication_reminder'
                );
                $sent++;
            }
        }

        $this->info("Medication reminders sent: {$sent}");

        return Command::SUCCESS;
    }
}
