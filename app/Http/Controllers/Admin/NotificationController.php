<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationBroadcast;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('notification-table')) {
            abort(403);
        }

        $query = Notification::with(['user', 'sentBy'])->whereNotNull('sent_by')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhereHas('user', fn ($sq) => $sq->where('name', 'like', "%{$request->search}%"));
            });
        }

        $notifications = $query->paginate(20)->withQueryString();

        $broadcasts = NotificationBroadcast::with('sentBy')->latest()->take(10)->get();

        return view('admin.notifications.index', compact('notifications', 'broadcasts'));
    }

    public function create()
    {
        if (!auth()->user()->can('notification-add')) {
            abort(403);
        }

        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name', 'phone']);

        return view('admin.notifications.create', compact('users'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('notification-add')) {
            abort(403);
        }

        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'body'    => 'required|string',
            'target'  => 'required|in:all,specific',
            'user_id' => 'required_if:target,specific|nullable|exists:users,id',
        ]);

        $adminId = auth('admin')->id();

        if ($data['target'] === 'all') {
            // Sending to every user in this request timed out with a large user
            // base — queue it for `notifications:send-broadcasts` (cron) instead.
            NotificationBroadcast::create([
                'title'       => $data['title'],
                'body'        => $data['body'],
                'status'      => 'pending',
                'total_users' => User::where('is_active', true)->count(),
                'sent_by'     => $adminId,
            ]);

            return redirect()->route('admin.notifications.index')
                ->with('success', 'سيتم إرسال الإشعار لجميع المستخدمين في الخلفية خلال دقائق، ويمكنك متابعة تقدّم الإرسال من هذه الصفحة.');
        }

        FCMController::sendToUser($data['user_id'], $data['title'], $data['body'], 'general', 'personal', $adminId);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'تم إرسال الإشعار بنجاح');
    }
}
