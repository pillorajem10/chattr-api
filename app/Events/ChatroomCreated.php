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
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
        // Load relationships so frontend can access both users' names immediately
        $this->chatroom = $chatroom->load(['userOne', 'userTwo']);
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

    /**
     * Data passed to the broadcast.
     *
     * This ensures the frontend receives complete user data.
     */
    public function broadcastWith(): array
    {
        return [
            'chatroom' => [
                'id' => $this->chatroom->id,
                'cr_user_one_id' => $this->chatroom->cr_user_one_id,
                'cr_user_two_id' => $this->chatroom->cr_user_two_id,
                'user_one' => $this->chatroom->userOne,
                'user_two' => $this->chatroom->userTwo,
                'created_at' => $this->chatroom->created_at,
            ],
        ];
    }
}
