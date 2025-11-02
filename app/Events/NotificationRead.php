<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class NotificationRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The notification data to broadcast.
     *
     * @var array|object
     */
    public $notification;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $notification
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    /**
     * The channel to broadcast on.
     *
     * Each user has a private notifications channel for security.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->notification->notification_user_id);
    }

    /**
     * Event name used on the frontend listener.
     */
    public function broadcastAs()
    {
        return 'notification.read';
    }
}
