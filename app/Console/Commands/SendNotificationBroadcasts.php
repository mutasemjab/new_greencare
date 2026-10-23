<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\FCMController;
use App\Models\Notification;
use App\Models\NotificationBroadcast;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Delivers "send to all" notifications queued from the admin dashboard.
 *
 * Runs every minute from the scheduler. Each run works for at most
 * TIME_BUDGET seconds, saving progress after every chunk, so a big user
 * base is spread over several runs instead of one request timing out.
 */
class SendNotificationBroadcasts extends Command
{
    protected $signature = 'notifications:send-broadcasts';

    protected $description = 'Send queued admin broadcast notifications in the background';

    private const CHUNK = 200;          // users loaded per DB query
    private const PARALLEL = 50;        // concurrent FCM requests
    private const TIME_BUDGET = 50;     // seconds per run (cron fires every 60)

    public function handle()
    {
        $startedAt = microtime(true);

        while (microtime(true) - $startedAt < self::TIME_BUDGET) {
            $broadcast = NotificationBroadcast::whereIn('status', ['pending', 'sending'])
                ->orderBy('id')
                ->first();

            if (! $broadcast) {
                return self::SUCCESS;
            }

            try {
                $this->process($broadcast, $startedAt);
            } catch (Throwable $e) {
                Log::error('Broadcast notification failed', ['broadcast_id' => $broadcast->id, 'error' => $e->getMessage()]);
                $broadcast->update(['status' => 'failed', 'error' => $e->getMessage(), 'finished_at' => now()]);
            }
        }

        return self::SUCCESS;
    }

    private function process(NotificationBroadcast $broadcast, float $startedAt): void
    {
        if ($broadcast->status === 'pending') {
            $broadcast->update([
                'status'      => 'sending',
                'started_at'  => now(),
                'total_users' => User::where('is_active', true)->count(),
            ]);
        }

        while (microtime(true) - $startedAt < self::TIME_BUDGET) {
            $users = User::where('is_active', true)
                ->where('id', '>', $broadcast->last_user_id)
                ->orderBy('id')
                ->limit(self::CHUNK)
                ->get(['id', 'fcm_token']);

            if ($users->isEmpty()) {
                $broadcast->update(['status' => 'completed', 'finished_at' => now()]);
                $this->info("Broadcast #{$broadcast->id} completed: {$broadcast->sent_count} sent, {$broadcast->failed_count} not delivered.");
                return;
            }

            $delivered = [];
            $tokens = $users->filter(fn ($u) => $u->fcm_token)->pluck('fcm_token', 'id')->all();

            foreach (array_chunk($tokens, self::PARALLEL, true) as $batch) {
                $delivered += FCMController::sendMessageBatch($batch, $broadcast->title, $broadcast->body, 'general');
            }

            // In-app notification list rows, one bulk insert per chunk.
            $now = now();
            Notification::insert($users->map(fn ($u) => [
                'user_id'    => $u->id,
                'title'      => $broadcast->title,
                'body'       => $broadcast->body,
                'screen'     => 'general',
                'type'       => 'broadcast',
                'fcm_sent'   => ! empty($delivered[$u->id]),
                'is_read'    => false,
                'sent_by'    => $broadcast->sent_by,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all());

            $sent = count(array_filter($delivered));

            $broadcast->update([
                'last_user_id'    => $users->last()->id,
                'processed_count' => $broadcast->processed_count + $users->count(),
                'sent_count'      => $broadcast->sent_count + $sent,
                'failed_count'    => $broadcast->failed_count + ($users->count() - $sent),
            ]);
        }
    }
}
