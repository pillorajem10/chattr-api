<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class ReactionCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** 
     * The reaction data to broadcast.
     */
    public $reaction;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $reaction  The reaction model or payload.
     */
    public function __construct($reaction)
    {
        $this->reaction = $reaction;
    }

    /**
     * Define the broadcast channel.
     *
     * Using a private channel here means only authorized clients 
     * can listen for new reactions.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('reactions');
    }

    /**
     * Define the event name for the frontend listener.
     *
     * This keeps the frontend subscription syntax simple 
     * (e.g. `.listen('.reaction.created', callback)`).
     */
    public function broadcastAs()
    {
        return 'reaction.created';
    }
}
