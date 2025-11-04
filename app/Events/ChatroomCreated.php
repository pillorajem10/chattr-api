<?php

namespace App\Events;

use App\Models\Chatroom;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcasted when a new private chatroom is created.
 *
 * Notifies both participants (user one and user two)
 * that a new chatroom has been established.
 */
class ChatroomCreated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * The chatroom that was created.
     */
    public $chatroom;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Chatroom $chatroom
     */
    public function __construct(Chatroom $chatroom)
    {
        $this->chatroom = $chatroom;
    }

    /**
     * Broadcast the event to both users' private channels.
     *
     * Each participant (user_one and user_two) will receive the event.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chatrooms.' . $this->chatroom->cr_user_one_id),
            new PrivateChannel('chatrooms.' . $this->chatroom->cr_user_two_id),
        ];
    }

    /**
     * Broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'chatroom.created';
    }
}
