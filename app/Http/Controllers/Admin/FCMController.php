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

    public static function sendMessage($title, $body, $fcmToken, $userId, $screen = "order", $extraData = [])
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


}
