<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class CommentRemoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The comment data to broadcast.
     */
    public $comment;

    /**
     * Pass the removed comment to the event.
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Broadcast over a private channel.
     * 
     * Keeps comment removals secure and synced only 
     * for authorized users (e.g., post viewers).
     */
    public function broadcastOn()
    {
        return new PrivateChannel('comments');
    }

    /**
     * The event name used on the frontend listener.
     */
    public function broadcastAs()
    {
        return 'comment.removed';
    }
}
