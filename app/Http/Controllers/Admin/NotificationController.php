<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = DatabaseNotification::query()
            ->where('notifiable_type', get_class(Auth::user()))
            ->where('notifiable_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function read(DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== Auth::id()) {
            abort(403, 'অনুমতি নেই।');
        }

        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;

        return $url ? redirect($url) : redirect()->route('admin.notifications.index');
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()
            ->with('success', 'সব নোটিফিকেশন পড়া হয়েছে।');
    }
}
