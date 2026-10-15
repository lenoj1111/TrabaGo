<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Using ShouldBroadcastNow sends immediately without waiting on queue workers
class NotificationSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = [
            'id' => (string) $notification->notification_id,
            'notification_id' => $notification->notification_id,
            'user_id' => $notification->user_id,
            'title' => $notification->title,
            'message' => $notification->message,
            'type' => $notification->type ?? 'info',
            'is_read' => false,
            'created_at' => now()->toIso8601String(),
            'related_id' => $notification->related_id,
        ];
    }

    public function broadcastOn(): array
    {
        $userId = $this->notification['user_id'] ?? request()->user()?->user_id;

        return [
            new PrivateChannel('App.Models.User.' . $userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }
}
