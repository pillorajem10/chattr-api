<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ==========================================================
 * Event: CommentCreated
 * ----------------------------------------------------------
 * This event is broadcasted whenever a new comment 
 * is successfully created on a post.
 *
 * Purpose:
 * - Send real-time updates to authorized users viewing
 *   a specific post so they can see new comments instantly.
 *
 * Broadcasting Channel:
 * - Private channel: "comments"
 *
 * Broadcast Name:
 * - comment.created
 *
 * Payload:
 * - The comment data passed from the backend.
 * ==========================================================
 */
class CommentCreated implements ShouldBroadcast
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
     * @param  mixed  $comment  The newly created comment data.
     * @return void
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Define the private channel this event should broadcast on.
     *
     * Keeps comments secure so only authorized users
     * (such as those viewing the post) receive them in real time.
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
        return 'comment.created';
    }
}
