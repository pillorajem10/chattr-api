<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Events\NotificationRemoved;
use App\Events\ReactionCreated;
use App\Events\ReactionRemoved;
use App\Helpers\ResponseHelper;
use App\Helpers\TokenHelper;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Reaction;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    /**
     * Add a "like" reaction to a post.
     *
     * Validates the request, prevents duplicate reactions from the same user,
     * creates a new reaction record, and broadcasts the updated like count
     * to all connected clients in real time.
     */
    public function reactToPost(Request $request, $postId)
    {
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Ensure the target post exists
        $post = Post::find($postId);
        if (!$post) {
            return ResponseHelper::sendError('Post not found.', null, 404);
        }

        // Prevent duplicate reactions
        $existingReaction = Reaction::where('reaction_post_id', $postId)
            ->where('reaction_user_id', $user->id)
            ->first();

        if ($existingReaction) {
            return ResponseHelper::sendError('You have already reacted to this post.', null, 409);
        }

        // Create a new reaction entry
        $reaction = Reaction::create([
            'reaction_post_id' => $postId,
            'reaction_user_id' => $user->id,
            'reaction_type'    => 'like',
        ]);

        // Recalculate total likes
        $totalLikes = Reaction::where('reaction_post_id', $postId)->count();

        // Broadcast the updated like count to all connected clients
        broadcast(new ReactionCreated((object) [
            'post_id'    => $postId,
            'likesCount' => $totalLikes,
        ]));

        // Create a notification for the post owner (skip if reacting to own post)
        if ($post->user->id !== $user->id) {
            $notification = Notification::create([
                'notification_user_id' => $post->user->id,
                'notification_type'    => 'reaction',
                'notification_post_id' => $postId,
                'notification_message' => "{$user->user_fname} {$user->user_lname} reacted to your post.",
            ]);

            broadcast(new NotificationCreated($notification));
        }

        // Return the created reaction with success response
        return ResponseHelper::sendSuccess($reaction, 'Reaction added successfully.', 201);
    }

    /**
     * Retrieve all reactions for a specific post.
     *
     * Returns a collection of all users who reacted to the given post.
     */
    public function getReactionsForPost(Request $request, $postId)
    {
        $reactions = Reaction::where('reaction_post_id', $postId)
            ->with('user')
            ->get();

        return ResponseHelper::sendSuccess($reactions, 'Reactions retrieved successfully.', 200);
    }

    /**
     * Remove an existing "like" reaction from a post.
     *
     * Deletes the user's reaction record, updates the post's like count,
     * broadcasts the change in real time, and removes any associated notifications.
     */
    public function removeReactionToPost(Request $request, $reactionId)
    {
        if (!$reactionId) {
            return ResponseHelper::sendError('Reaction ID is required.', null, 400);
        }

        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Find the reaction belonging to the authenticated user
        $reaction = Reaction::where('id', $reactionId)
            ->where('reaction_user_id', $user->id)
            ->first();

        if (!$reaction) {
            return ResponseHelper::sendError('Reaction not found.', null, 404);
        }

        // Delete the reaction record
        $reaction->delete();

        // Recalculate the post's total like count
        $totalLikes = Reaction::where('reaction_post_id', $reaction->reaction_post_id)->count();

        // Broadcast the updated count to all clients
        broadcast(new ReactionRemoved((object) [
            'post_id'    => $reaction->reaction_post_id,
            'likesCount' => $totalLikes,
        ]));

        // Remove any related notification for this reaction
        $notification = Notification::where('notification_post_id', $reaction->reaction_post_id)
            ->where('notification_user_id', $reaction->reaction_user_id)
            ->where('notification_type', 'reaction')
            ->first();

        if ($notification) {
            broadcast(new NotificationRemoved($notification));
            $notification->delete();
        }

        return ResponseHelper::sendSuccess(null, 'Reaction removed successfully.', 200);
    }
}
