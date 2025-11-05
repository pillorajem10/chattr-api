<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ==========================================================
 * Event: NotificationRemoved
 * ----------------------------------------------------------
 * This event is broadcasted whenever an existing notification
 * is cleared or removed for a specific user.
 *
 * Purpose:
 * - Notify the intended user in real time that a notification
 *   has been deleted or marked as removed.
 * - Maintain synchronization between server and client
 *   notification states.
 *
 * Broadcasting Channel:
 * - Private channel: "notifications.{notification_user_id}"
 *
 * Broadcast Name:
 * - notification.removed
 *
 * Payload:
 * - The removed notification data.
 * ==========================================================
 */
class NotificationRemoved implements ShouldBroadcast
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
     * @param  mixed  $notification  The removed notification instance.
     * @return void
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    /**
     * Define the private channel this event should broadcast on.
     *
     * Ensures that only authorized clients (e.g., the intended user)
     * receive updates when a notification is removed.
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
     * Example usage on the client:
     * `.listen('.notification.removed', callback)`
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'notification.removed';
    }
}
