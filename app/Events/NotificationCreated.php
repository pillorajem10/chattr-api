<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ==========================================================
 * Event: NotificationCreated
 * ----------------------------------------------------------
 * This event is broadcasted whenever a new notification
 * is created for a user.
 *
 * Purpose:
 * - Deliver real-time notification updates to the intended user.
 * - Ensure each notification is securely transmitted through
 *   private user-specific channels.
 *
 * Broadcasting Channel:
 * - Private channel: "notifications.{notification_user_id}"
 *
 * Broadcast Name:
 * - notification.created
 *
 * Payload:
 * - The new notification data.
 * ==========================================================
 */
class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The notification data to broadcast.
     *
     * @var mixed
     */
    public $notification;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $notification  The new notification instance.
     * @return void
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    /**
     * Define the private channel this event should broadcast on.
     *
     * Keeps notifications secure so that only the intended user
     * (or authorized clients) can receive them in real time.
     *
     * @return \Illuminate\Broadcasting\PrivateChannel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->notification->notification_user_id);
    }

    /**
     * Define the event name used by frontend listeners.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'notification.created';
    }
}
