<?php

namespace App\Http\Controllers\Admin;

use App\Models\Notification;
use App\Models\User;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FCMController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('permission:fcm-send');
    }

    /**
     * Google OAuth access tokens for this service account are valid ~1 hour.
     * Caching avoids a network round-trip to Google for every single
     * notification, which matters a lot once "send to all" loops over
     * hundreds of users in one request.
     */
    private static function getAccessToken(): string
    {
        return Cache::remember('fcm_access_token', 3000, function () {
            $client = new GoogleClient();
            $client->setAuthConfig(base_path('json/greencarenew-85553fb39c71.json'));
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->useApplicationDefaultCredentials();
            $client->fetchAccessTokenWithAssertion();

            return $client->getAccessToken()['access_token'];
        });
    }

    /**
     * $options:
     *  - 'tag'  string  Collapses notifications that share it: Android replaces
     *                   the shown notification with the same tag, iOS groups
     *                   them in one thread (used for one-notification-per-chat).
     */
    public static function sendMessage($title, $body, $fcmToken, $userId, $screen = "order", $extraData = [], array $options = [])
    {
        if (!$fcmToken) {
            Log::error("FCM Error: No FCM token provided for user ID $userId");
            return false;
        }

        try {
            $access_token = self::getAccessToken();

            $headers = [
                "Authorization: Bearer $access_token",
                'Content-Type: application/json'
            ];

            $data = [
                "message" => [
                    "token" => $fcmToken,
                    "notification" => [
                        "title" => $title,
                        "body" => $body
                    ],
                    "data" => array_merge([
                        'screen' => $screen,
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK"
                    ], array_map('strval', $extraData)),
                    "android" => [
                        "priority" => "high"
                    ]
                ]
            ];

            if (!empty($options['tag'])) {
                $data['message']['android']['notification'] = ['tag' => $options['tag']];
                $data['message']['android']['collapse_key'] = $options['tag'];
                $data['message']['apns'] = [
                    'headers' => ['apns-collapse-id' => $options['tag']],
                    'payload' => ['aps' => ['thread-id' => $options['tag'], 'sound' => 'default']],
                ];
            }

            $payload = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/greencarenew/messages:send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output for debugging
            $result = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if ($result === false || $err) {
                Log::error("FCM Error for user ID $userId: cURL Error: " . $err);
                return false;
            } else {
                $response = json_decode($result, true);
                Log::info("FCM Response for user ID $userId: " . json_encode($response));
                if (isset($response['name'])) {
                    return true;
                } else {
                    Log::error("FCM Error for user ID $userId: " . json_encode($response));
                    if (isset($response['error']['details'][0]['errorCode']) && $response['error']['details'][0]['errorCode'] === 'UNREGISTERED') {
                        Log::info("FCM token cleanup for user ID $userId");
                        User::where('id', $userId)->update(['fcm_token' => null]);
                    }
                    return false;
                }
            }
        } catch (\Exception $e) {
            Log::error("FCM Error for user ID $userId: " . $e->getMessage());
            return false;
        }
    }


    /**
     * Sends the same push to many devices in parallel (curl_multi), which is
     * far faster than one blocking request per user. Clears tokens FCM
     * reports as UNREGISTERED.
     *
     * @param  array<int, string>  $tokens  user_id => fcm_token
     * @return array<int, bool>             user_id => delivered
     */
    public static function sendMessageBatch(array $tokens, $title, $body, $screen = 'general', $extraData = []): array
    {
        $results = array_fill_keys(array_keys($tokens), false);

        if (empty($tokens)) {
            return $results;
        }

        // Let a credentials failure propagate: the caller marks the broadcast
        // as failed instead of reporting every user as "not delivered".
        $accessToken = self::getAccessToken();

        $multi   = curl_multi_init();
        $handles = [];

        foreach ($tokens as $userId => $token) {
            $payload = json_encode([
                'message' => [
                    'token'        => $token,
                    'notification' => ['title' => $title, 'body' => $body],
                    'data'         => array_merge([
                        'screen'       => $screen,
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ], array_map('strval', $extraData)),
                    'android'      => ['priority' => 'high'],
                ],
            ]);

            $ch = curl_init('https://fcm.googleapis.com/v1/projects/greencarenew/messages:send');
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => ["Authorization: Bearer $accessToken", 'Content-Type: application/json'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT        => 20,
                CURLOPT_POSTFIELDS     => $payload,
            ]);

            curl_multi_add_handle($multi, $ch);
            $handles[$userId] = $ch;
        }

        do {
            $status = curl_multi_exec($multi, $running);
            if ($running) {
                curl_multi_select($multi, 1.0);
            }
        } while ($running && $status === CURLM_OK);

        $unregistered = [];

        foreach ($handles as $userId => $ch) {
            $response = json_decode((string) curl_multi_getcontent($ch), true);

            if (isset($response['name'])) {
                $results[$userId] = true;
            } elseif (($response['error']['details'][0]['errorCode'] ?? null) === 'UNREGISTERED') {
                $unregistered[] = $userId;
            } elseif ($err = curl_error($ch)) {
                Log::error("FCM batch error for user ID $userId: $err");
            }

            curl_multi_remove_handle($multi, $ch);
            curl_close($ch);
        }

        curl_multi_close($multi);

        if ($unregistered) {
            User::whereIn('id', $unregistered)->update(['fcm_token' => null]);
        }

        return $results;
    }

     public static function sendMessageToAll($title, $body, $screen = "order")
    {
        $users = User::whereNotNull('fcm_token')->get();
        
        if ($users->isEmpty()) {
            Log::warning("No users with FCM tokens found");
            return false;
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($users as $user) {
            $result = self::sendMessage($title, $body, $user->fcm_token, $user->id, $screen);
            if ($result) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        Log::info("FCM Bulk Send - Success: $successCount, Failed: $failCount");
        
        return $successCount > 0; // Return true if at least one notification was sent
    }

    /**
     * Sends a push to one user AND persists a row in the notifications
     * table, so it shows up in the app's in-app notification list
     * regardless of whether the push itself succeeds (missing/expired
     * token, offline device, etc).
     */
    public static function sendToUser($userId, $title, $body, $screen = "order", $type = 'general', $sentBy = null, $data = [])
    {
        $user = User::find($userId);
        $sent = false;

        if (!$user) {
            Log::error("User not found for user ID: $userId");
            return false;
        }

        if ($user->fcm_token) {
            $sent = self::sendMessage($title, $body, $user->fcm_token, $user->id, $screen, $data);
        } else {
            Log::error("No FCM token for user ID: $userId");
        }

        Notification::create([
            'user_id'  => $userId,
            'title'    => $title,
            'body'     => $body,
            'screen'   => $screen,
            'data'     => $data ?: null,
            'type'     => $type,
            'fcm_sent' => $sent,
            'sent_by'  => $sentBy,
        ]);

        return $sent;
    }

    /**
     * WhatsApp-style chat push: one notification per room per user instead
     * of one per message. The user's unread chat_message row for the room
     * is updated in place (message count + last few lines) and the push is
     * sent with a per-room tag so the device replaces the previous one.
     */
    public static function sendChatMessageToUser($userId, int $roomId, string $roomTitle, string $line, $sentBy = null)
    {
        $user = User::find($userId);

        if (!$user) {
            Log::error("User not found for user ID: $userId");
            return false;
        }

        $existing = Notification::where('user_id', $userId)
            ->where('type', 'chat_message')
            ->where('is_read', false)
            ->where('data->room_id', $roomId)
            ->latest('id')
            ->first();

        $count    = $existing ? ((int) ($existing->data['count'] ?? 1)) + 1 : 1;
        $previous = $existing ? ($existing->data['lines'] ?? [$existing->body]) : [];
        // Newest first so the collapsed notification previews the latest message.
        $lines = array_values(array_filter(
            array_slice(array_merge([$line], $previous), 0, 5),
            fn ($l) => $l !== null && $l !== ''
        ));

        $title = $count > 1 ? "{$roomTitle} ({$count} رسائل جديدة)" : $roomTitle;
        $body  = implode("\n", $lines);
        $data  = ['room_id' => $roomId, 'count' => $count, 'lines' => $lines];

        $sent = false;
        if ($user->fcm_token) {
            $sent = self::sendMessage(
                $title, $body, $user->fcm_token, $user->id, 'chat_message',
                ['room_id' => $roomId],
                ['tag' => "room_{$roomId}"]
            );
        }

        $attributes = [
            'title'    => $title,
            'body'     => $body,
            'screen'   => 'chat_message',
            'data'     => $data,
            'fcm_sent' => $sent,
            'sent_by'  => $sentBy,
        ];

        if ($existing) {
            // Bump created_at so the grouped row moves to the top of the list.
            $existing->forceFill($attributes + ['created_at' => now()])->save();
        } else {
            Notification::create($attributes + ['user_id' => $userId, 'type' => 'chat_message']);
        }

        return $sent;
    }
}
