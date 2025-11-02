<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Broadcast to the receiver's private channel only.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('messages.' . $this->message->message_receiver_id);
    }

    /**
     * Broadcast event name.
     */
    public function broadcastAs()
    {
        return 'message.sent';
    }
}
