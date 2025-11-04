<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentCreated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * The comment data to broadcast.
     */
    public $comment;

    /**
     * Pass the newly created comment to the event.
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Broadcast over a private channel.
     *
     * Keeps comments secure so only authorized users
     * (like those viewing the post) receive them in real time.
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
        return 'comment.created';
    }
}
