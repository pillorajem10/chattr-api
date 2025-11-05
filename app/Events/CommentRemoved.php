<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ==========================================================
 * Event: CommentRemoved
 * ----------------------------------------------------------
 * This event is broadcasted whenever a comment is deleted
 * or removed from a post.
 *
 * Purpose:
 * - Notify authorized users in real time that a comment
 *   has been removed.
 * - Keep post comment sections synchronized across clients
 *   without manual refresh.
 *
 * Broadcasting Channel:
 * - Private channel: "comments"
 *
 * Broadcast Name:
 * - comment.removed
 *
 * Payload:
 * - The removed comment data.
 * ==========================================================
 */
class CommentRemoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The comment data to broadcast.
     *
     * @var mixed
     */
    public $comment;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $comment  The removed comment data.
     * @return void
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Define the private channel this event should broadcast on.
     *
     * Ensures that comment removal updates are sent only
     * to authorized users (e.g., post viewers).
     *
     * @return \Illuminate\Broadcasting\PrivateChannel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('comments');
    }

    /**
     * Define the event name used by frontend listeners.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'comment.removed';
    }
}
