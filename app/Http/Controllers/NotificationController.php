<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread notification count for current user
     */
    public function getCount()
    {
        $count = Notification::getUnreadCount(Auth::id());
        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications for current user
     */
    public function index()
    {
        $notifications = Notification::getRecentForUser(Auth::id(), 20);
        return response()->json($notifications);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::find($id);
        
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 404);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $userId = Auth::id();
        
        Notification::where(function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->orWhereNull('user_id');
        })
        ->where('is_read', false)
        ->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = Notification::find($id);
        
        if ($notification) {
            $notification->delete();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 404);
    }
}
