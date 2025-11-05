<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ==========================================================
 * Event: CommentCreated
 * ----------------------------------------------------------
 * Broadcasts whenever a new comment is successfully created.
 * 
 * Channel: public "comments"
 * Name: comment.created
 * Payload: comment data + updated comment count
 * ==========================================================
 */
class CommentCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The comment data.
     */
    public $comment;

    /**
     * The updated total comment count for the post.
     */
    public $commentCount;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $comment
     * @param  int    $commentCount
     */
    public function __construct($comment, $commentCount)
    {
        $this->comment = $comment;
        $this->commentCount = $commentCount;
    }

    /**
     * Broadcast on the public "comments" channel.
     */
    public function broadcastOn()
    {
        return new Channel('comments');
    }

    /**
     * Broadcast payload.
     */
    public function broadcastWith()
    {
        return [
            'comment' => [
                'id' => $this->comment->id,
                'comment_post_id' => $this->comment->comment_post_id,
                'comment_content' => $this->comment->comment_content,
                'created_at' => $this->comment->created_at,
                'user' => [
                    'id' => $this->comment->user->id,
                    'user_fname' => $this->comment->user->user_fname,
                    'user_lname' => $this->comment->user->user_lname,
                ],
            ],
            'commentCount' => $this->commentCount,
        ];
    }

    /**
     * Event name listened to on the frontend.
     */
    public function broadcastAs()
    {
        return 'comment.created';
    }
}
