<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * The notification data to broadcast.
     */
    public $notification;

    /**
     * Pass the new notification instance to the event.
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    /**
     * Broadcast over a private channel.
     *
     * This keeps notifications secure so that only
     * the intended user (or authorized clients) receive them.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->notification->notification_user_id);
    }

    /**
     * The event name used on the frontend listener.
     */
    public function broadcastAs()
    {
        return 'notification.created';
    }
}
