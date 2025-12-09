<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Create notification for specific user
     */
    public static function createForUser($userId, $type, $title, $message, $data = null)
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Create notification for all staff
     */
    public static function createForAllStaff($type, $title, $message, $data = null)
    {
        return self::create([
            'user_id' => null,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Get unread count for user
     */
    public static function getUnreadCount($userId)
    {
        return self::where(function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->orWhereNull('user_id');
        })
        ->where('is_read', false)
        ->count();
    }

    /**
     * Get recent notifications for user
     */
    public static function getRecentForUser($userId, $limit = 10)
    {
        return self::where(function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->orWhereNull('user_id');
        })
        ->orderBy('created_at', 'desc')
        ->limit($limit)
        ->get();
    }
}
