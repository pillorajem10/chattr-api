<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcasted when messages from a specific sender
 * have been marked as read by the receiver.
 *
 * Notifies both the original sender and receiver
 * in real time that messages in the chatroom are now read.
 */
class MessageRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The original sender’s user ID.
     */
    public $senderId;

    /**
     * The user who read the messages.
     */
    public $receiverId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $senderId, int $receiverId)
    {
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
    }

    /**
     * Broadcast to both users' unified private channels.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->senderId),
            new PrivateChannel('user.' . $this->receiverId),
        ];
    }

    /**
     * Event name for Echo.
     */
    public function broadcastAs(): string
    {
        return 'message.read';
    }

    /**
     * Data payload sent to the frontend.
     */
    public function broadcastWith(): array
    {
        return [
            'sender_id'   => $this->senderId,
            'receiver_id' => $this->receiverId,
            'status'      => 'read',
        ];
    }
}
