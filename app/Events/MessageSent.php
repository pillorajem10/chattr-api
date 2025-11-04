<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

/**
 * Broadcasted when a private message is sent.
 *
 * Notifies the receiver in real time about the new message.
 */
class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The message being broadcast.
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Broadcast to the receiver's private channel only.
     *
     * Example: messages.{receiver_id}
     */
    public function broadcastOn()
    {
        return new PrivateChannel('messages.' . $this->message->message_receiver_id);
    }

    /**
     * Broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Data passed to the broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id'                  => $this->message->id,
            'message_chatroom_id' => $this->message->message_chatroom_id,
            'message_sender_id'   => $this->message->message_sender_id,
            'message_receiver_id' => $this->message->message_receiver_id,
            'message_content'     => $this->message->message_content,
            'message_read'        => $this->message->message_read,
            'created_at'          => $this->message->created_at->toDateTimeString(),
        ];
    }
}
