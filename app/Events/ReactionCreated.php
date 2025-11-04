<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReactionCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reaction;

    public function __construct($reaction)
    {
        $this->reaction = $reaction;
    }

    public function broadcastOn()
    {
        return new Channel('reactions');
    }

    public function broadcastAs()
    {
        return 'reaction.created';
    }
}
