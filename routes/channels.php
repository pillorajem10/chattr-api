<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
| This file defines all private broadcast channels used in the app.
| Each channel ensures that only the correct, authenticated user
| can listen to events that belong to them.
|--------------------------------------------------------------------------
*/

/**
 * Notifications Channel
 * ------------------------------------------------------------
 * Used for sending and receiving real-time notifications
 * for a specific user. Only the user whose ID matches the
 * channel parameter can listen on this channel.
 *
 * Used by:
 * - NotificationCreated
 * - NotificationRead
 * - NotificationRemoved
 */
Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

/**
 * Messages Channel
 * ------------------------------------------------------------
 * Handles private messages between users. Each user has their
 * own message channel, ensuring messages are delivered securely
 * and only to the correct recipient.
 *
 * Used by:
 * - MessageSent
 * - MessageRead
 */
Broadcast::channel('messages.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

/**
 * Chatrooms Channel
 * ------------------------------------------------------------
 * Used to notify both participants when a new chatroom (private
 * conversation) has been created. Only the users involved in
 * that chatroom can listen to this channel.
 *
 * Used by:
 * - ChatroomCreated
 */
Broadcast::channel('chatrooms.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

/**
 * Reactions Channel
 * ------------------------------------------------------------
 * Sends live updates whenever someone reacts to a post.
 * Any authenticated user can listen to reactions for a
 * specific post.
 *
 * Used by:
 * - ReactionCreated
 * - ReactionRemoved
 */
Broadcast::channel('reactions.{postId}', function ($user, $postId) {
    return !is_null($user);
});

/**
 * Comments Channel
 * ------------------------------------------------------------
 * Broadcasts new or deleted comments for a specific post
 * in real time. Any logged-in user can listen to comment
 * updates for that post.
 *
 * Used by:
 * - CommentCreated
 * - CommentRemoved
 */
Broadcast::channel('comments.{postId}', function ($user, $postId) {
    return !is_null($user);
});
