<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReactionRemoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The reaction model or payload.
     */
    public $reaction;

    /**
     * Create a new event instance.
     *
     * @param mixed $reaction
     */
    public function __construct($reaction)
    {
        $this->reaction = $reaction;
    }

    /**
     * Define the broadcast channel.
     *
     * This is now a public channel so that any client
     * can listen to reaction updates for a post.
     */
    public function broadcastOn()
    {
        return new Channel('reactions.' . $this->reaction->post_id);
    }

    /**
     * Define the event name for frontend listeners.
     */
    public function broadcastAs()
    {
        return 'reaction.removed';
    }
}
