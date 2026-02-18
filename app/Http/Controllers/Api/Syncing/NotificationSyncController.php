<?php

namespace App\Http\Controllers\Api\Syncing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationSyncController extends Controller
{
    /**
     * Display a listing of the notifications.
     */
    public function pull(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(10);
        return response()->json($notifications);
    }

    /**
     * Display the specified notification.
     */
    public function show(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);

        // Mark as read when viewing details
        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        return response()->json([
            'notification' => $notification
        ]);
    }

    /**
     * Mark the specified notification as read.
     */
    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);

        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        return response()->json(['message' => 'Notification marquée comme lue.']);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'Toutes les notifications ont été marquées comme lues.']);
    }
}
