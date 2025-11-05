<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ==========================================================
 * Event: ReactionRemoved
 * ----------------------------------------------------------
 * This event is broadcasted whenever an existing reaction
 * (e.g., like, heart, etc.) is removed from a post or comment.
 *
 * Purpose:
 * - Notify all connected clients in real time that a reaction
 *   has been removed.
 * - Ensure reaction counts and states remain synchronized
 *   across all user interfaces.
 *
 * Broadcasting Channel:
 * - Public channel: "reactions"
 *
 * Broadcast Name:
 * - reaction.removed
 *
 * Payload:
 * - The removed reaction data.
 * ==========================================================
 */
class ReactionRemoved implements ShouldBroadcast
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
     * @param  mixed  $reaction  The removed reaction instance.
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
     * `.listen('.reaction.removed', callback)`
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'reaction.removed';
    }
}
