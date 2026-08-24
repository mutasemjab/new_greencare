<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NotificationResource;
use App\Http\Traits\ApiResponse;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user('user-api')->id)
            ->latest()
            ->paginate(20);

        return $this->success(NotificationResource::collection($notifications)->response()->getData(true));
    }

    public function unreadCount(Request $request)
    {
        $count = Notification::where('user_id', $request->user('user-api')->id)->unread()->count();

        return $this->success(['count' => $count]);
    }

    public function markRead(Request $request, int $id)
    {
        $notification = Notification::where('user_id', $request->user('user-api')->id)->findOrFail($id);

        $notification->update(['is_read' => true, 'read_at' => now()]);

        return $this->success(new NotificationResource($notification));
    }

    public function markAllRead(Request $request)
    {
        Notification::where('user_id', $request->user('user-api')->id)
            ->unread()
            ->update(['is_read' => true, 'read_at' => now()]);

        return $this->success(null, 'تم تحديد الكل كمقروء');
    }
}
