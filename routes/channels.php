<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Chatroom;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
| Defines all private broadcast channels used in the app.
| Each channel ensures that only the correct, authenticated user
| can listen to events that belong to them.
|--------------------------------------------------------------------------
*/

/**
 * Notifications Channel
 * ------------------------------------------------------------
 * Used for sending and receiving real-time notifications
 * for a specific user.
 *
 * Used by:
 * - NotificationCreated
 */
Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

/**
 * Unified User Channel
 * ------------------------------------------------------------
 * Each authenticated user subscribes once to this channel.
 * All message and chatroom events are broadcasted here, so
 *
 * Used by:
 * - MessageSent
 * - MessageRead
 * - ChatroomCreated
 */
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

/**
 * Reactions Channel
 * ------------------------------------------------------------
 * Sends live updates whenever someone reacts to a post.
 *
 * Used by:
 * - ReactionCreated
 * - ReactionRemoved
 */
Broadcast::channel('reactions', function () {
    return true;
});

/**
 * Comments Channel
 * ------------------------------------------------------------
 * Broadcasts new or deleted comments for a specific post.
 *
 * Used by:
 * - CommentCreated
 * - CommentRemoved
 */
Broadcast::channel('comments.{postId}', function ($user, $postId) {
    return !is_null($user);
});
