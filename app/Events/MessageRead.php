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
 * Notifies the original sender in real-time that their
 * messages in the chatroom are now read.
 */
class MessageRead implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * The ID of the original message sender.
     *
     * @var int
     */
    public $senderId;

    /**
     * The ID of the user who read the messages.
     *
     * @var int
     */
    public $receiverId;

    /**
     * Create a new event instance.
     *
     * @param int $senderId
     * @param int $receiverId
     */
    public function __construct($senderId, $receiverId)
    {
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
    }

    /**
     * Broadcast to the sender's private channel.
     *
     * Example: messages.{sender_id}
     */
    public function broadcastOn()
    {
        return new PrivateChannel('messages.' . $this->senderId);
    }

    /**
     * Broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'message.read';
    }

    /**
     * Data sent to the frontend when event is received.
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
