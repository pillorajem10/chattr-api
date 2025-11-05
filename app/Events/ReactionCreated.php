<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ==========================================================
 * Event: ReactionCreated
 * ----------------------------------------------------------
 * This event is broadcasted whenever a new reaction 
 * (e.g., like, heart, etc.) is added to a post or comment.
 *
 * Purpose:
 * - Notify connected clients in real time that a reaction 
 *   was created.
 * - Keep reaction counts synchronized across users’ screens.
 *
 * Broadcasting Channel:
 * - Public channel: "reactions"
 *
 * Broadcast Name:
 * - reaction.created
 *
 * Payload:
 * - The newly created reaction data.
 * ==========================================================
 */
class ReactionCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The reaction data to broadcast.
     *
     * @var mixed
     */
    public $reaction;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $reaction  The newly created reaction instance.
     * @return void
     */
    public function __construct($reaction)
    {
        $this->reaction = $reaction;
    }

    /**
     * Define the public channel this event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        return new Channel('reactions');
    }

    /**
     * Define the event name used by frontend listeners.
     *
     * Example usage:
     * `.listen('.reaction.created', callback)`
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'reaction.created';
    }
}
