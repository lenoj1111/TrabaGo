<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Format a Notification model for API responses.
     */
    private function formatNotification(Notification $notif): array
    {
        return [
            'id' => (string)$notif->notification_id,
            'notification_id' => $notif->notification_id,
            'title' => $notif->title,
            'message' => $notif->message,
            'type' => $notif->type ?? 'approval',
            'read' => (bool)$notif->is_read,
            'is_read' => (bool)$notif->is_read,
            'createdAt' => $notif->created_at ? $notif->created_at->toIso8601String() : now()->toIso8601String(),
            'created_at' => $notif->created_at ? $notif->created_at->toIso8601String() : now()->toIso8601String(),
            'related_id' => $notif->related_id,
        ];
    }

    /**
     * Get all notifications for the authenticated user.
     */
    public function getAll(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([]);
        }

        $notifications = Notification::where('user_id', $user->user_id)
            ->orderByDesc('notification_id')
            ->get()
            ->map(function ($n) {
                return $this->formatNotification($n);
            });

        return response()->json($notifications);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        
        $query = Notification::where('notification_id', $id);
        if ($user) {
            $query->where('user_id', $user->user_id);
        }

        $notif = $query->first();
        if ($notif) {
            $notif->is_read = true;
            $notif->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }
}
