<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcasted when a private message is sent.
 *
 * Notifies the intended receiver in real time with full message details.
 */
class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The message being broadcast.
     *
     * @var \App\Models\Message
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Message $message
     */
    public function __construct(Message $message)
    {
        // Eager load the sender relationship so frontend can display their name or avatar
        $this->message = $message->load('sender');
    }

    /**
     * Broadcast to the receiver's private channel.
     *
     * Example: messages.{receiver_id}
     */
    public function broadcastOn()
    {
        return new PrivateChannel('chatroom.' . $this->message->message_chatroom_id);
    }

    /**
     * The event name to be used by Laravel Echo.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Data payload sent to the frontend.
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
            'sender'              => $this->message->sender, 
            'created_at'          => $this->message->created_at->toDateTimeString(),
        ];
    }
}
