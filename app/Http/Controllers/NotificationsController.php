<?php

namespace App\Http\Controllers;

use App\Models\Notifications;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Notifications::where('user_id', Auth::id())
                ->latest()
                ->get()
        ]);
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => Notifications::where('user_id', Auth::id())
                ->where('is_read', false)
                ->count()
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notifications::where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notifications::where('user_id', Auth::id())
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
