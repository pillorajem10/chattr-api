<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class MessageRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $senderId;
    public $receiverId;

    /**
     * Create a new event instance.
     */
    public function __construct($senderId, $receiverId)
    {
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
    }

    /**
     * Broadcast to the sender's private channel.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('messages.' . $this->senderId);
    }

    /**
     * Event alias name.
     */
    public function broadcastAs()
    {
        return 'message.read';
    }

    /**
     * Data sent to the frontend.
     */
    public function broadcastWith()
    {
        return [
            'sender_id' => $this->senderId,
            'receiver_id' => $this->receiverId,
            'status' => 'read'
        ];
    }
}
