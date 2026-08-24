<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::with(['user', 'sentBy'])->whereNotNull('sent_by')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhereHas('user', fn ($sq) => $sq->where('name', 'like', "%{$request->search}%"));
            });
        }

        $notifications = $query->paginate(20)->withQueryString();

        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name', 'phone']);

        return view('admin.notifications.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'body'    => 'required|string',
            'target'  => 'required|in:all,specific',
            'user_id' => 'required_if:target,specific|nullable|exists:users,id',
        ]);

        $adminId = auth('admin')->id();

        if ($data['target'] === 'all') {
            $users = User::where('is_active', true)->get(['id']);

            foreach ($users as $user) {
                FCMController::sendToUser($user->id, $data['title'], $data['body'], 'general', 'broadcast', $adminId);
            }

            return redirect()->route('admin.notifications.index')
                ->with('success', "تم إرسال الإشعار لـ {$users->count()} مستخدم");
        }

        FCMController::sendToUser($data['user_id'], $data['title'], $data['body'], 'general', 'personal', $adminId);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'تم إرسال الإشعار بنجاح');
    }
}
