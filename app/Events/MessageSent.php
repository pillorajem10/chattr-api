<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message->load('sender', 'receiver', 'chatroom');
    }

    /**
     * The event name used on the frontend.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Channels to broadcast to.
     * Each user only needs one unified channel.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->message->sender->id),
            new PrivateChannel('user.' . $this->message->receiver->id),
        ];
    }

    /**
     * Data payload sent to the client.
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'message_chatroom_id' => $this->message->message_chatroom_id,
                'message_sender_id' => $this->message->message_sender_id,
                'message_content' => $this->message->message_content,
                'created_at' => $this->message->created_at->toDateTimeString(),
                'sender' => $this->message->sender,
                'receiver' => $this->message->receiver,
            ],
        ];
    }
}
