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
 * Notifies the original sender in real time that their
 * messages in the chatroom are now read.
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
     * Broadcast to the sender’s private message channel.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('messages.' . $this->senderId);
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
