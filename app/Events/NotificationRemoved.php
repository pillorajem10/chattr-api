<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class NotificationRemoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The notification data to broadcast.
     */
    public $notification;

    /**
     * Pass the removed notification instance to the event.
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    /**
     * Broadcast over a private channel.
     * 
     * This ensures only authorized clients (like the intended user)
     * receive real-time updates when a notification is cleared or removed.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->notification->notification_user_id);
    }

    /**
     * The event name used on the frontend listener.
     * 
     * Example listener:
     * `.listen('.notification.removed', callback)`
     */
    public function broadcastAs()
    {
        return 'notification.removed';
    }
}
