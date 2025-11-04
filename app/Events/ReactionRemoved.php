<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReactionRemoved implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * The reaction data to broadcast.
     */
    public $reaction;

    /**
     * Create a new event instance.
     *
     * @param mixed $reaction The reaction model or payload.
     */
    public function __construct($reaction)
    {
        $this->reaction = $reaction;
    }

    /**
     * Define the broadcast channel.
     *
     * Only authorized clients can listen for reaction removals.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('reactions');
    }

    /**
     * Define the event name for the frontend listener.
     *
     * Example usage on the client:
     * `.listen('.reaction.removed', callback)`
     */
    public function broadcastAs()
    {
        return 'reaction.removed';
    }
}
