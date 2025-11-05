<?php

namespace App\Events;

use App\Models\Chatroom;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ==========================================================
 * Event: ChatroomCreated
 * ----------------------------------------------------------
 * This event is broadcasted when a new private chatroom 
 * is successfully created between two users.
 *
 * Purpose:
 * - Notify both participants (User One and User Two)
 *   that a new private chatroom has been established.
 * - Allow the frontend to instantly display the new
 *   chatroom information without requiring manual refresh.
 * 
 * Broadcasting Channels:
 * - user.{cr_user_one_id}
 * - user.{cr_user_two_id}
 *
 * Broadcast Name:
 * - chatroom.created
 * 
 * Payload:
 * - Chatroom details (IDs, user relationships, timestamps)
 * ==========================================================
 */
class ChatroomCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The chatroom instance that was created.
     *
     * @var \App\Models\Chatroom
     */
    public $chatroom;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Chatroom  $chatroom
     * @return void
     */
    public function __construct(Chatroom $chatroom)
    {
        // Preload user relationships so frontend can access user names immediately
        $this->chatroom = $chatroom->load(['userOne', 'userTwo']);
    }

    /**
     * Determine the private channels this event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->chatroom->cr_user_one_id),
            new PrivateChannel('user.' . $this->chatroom->cr_user_two_id),
        ];
    }

    /**
     * The event’s broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'chatroom.created';
    }

    /**
     * Data that will be sent with the broadcast.
     *
     * @return array<string, mixed>
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
