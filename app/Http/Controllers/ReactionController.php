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
     * Add a reaction to a post.
     *
     *
     * Validates input data and creates a new reaction in the database.
     */
    public function reactToPost(Request $request, $postId)
    {
        // Decode the token from the Authorization header
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Verify that the post exists
        $post = Post::find($postId);
        if (! $post) {
            return ResponseHelper::sendError('Post not found.', null, 404);
        }

        // Check if the user already reacted to the post
        $existingReaction = Reaction::where('reaction_post_id', $postId)
            ->where('reaction_user_id', $user->id)
            ->first();

        if ($existingReaction) {
            return ResponseHelper::sendError('You have already reacted to this post.', null, 409);
        }

        // Create new reaction
        $reaction = Reaction::create([
            'reaction_post_id' => $postId,
            'reaction_user_id' => $user->id,
            'reaction_type'    => 'like',
        ]);

        // Fire the real-time reaction event
        event(new ReactionCreated($reaction));
        broadcast(new ReactionCreated($reaction));

        // Notify the post owner (but skip self-reactions)
        if ($post->user->id !== $user->id) {
            $notification = Notification::create([
                'notification_user_id' => $post->user->id,
                'notification_type'    => 'reaction',
                'notification_post_id' => $postId,
                'notification_message' => "{$user->user_fname} {$user->user_lname} reacted to your post.",
            ]);

            // Fire the real-time notification event
            event(new NotificationCreated($notification));
        }

        return ResponseHelper::sendSuccess($reaction, 'Reaction added successfully.', 201);
    }

    /**
     * Get reactions for a posts
     *
     */
    public function getReactionsForPost(Request $request, $postId)
    {
        // Fetch reactions for the post
        $reactions = Reaction::where('reaction_post_id', $postId)
            ->with('user')
            ->get();

        // Return response
        return ResponseHelper::sendSuccess($reactions, 'Reactions retrieved successfully.', 200);
    }

    /**
     * Remove existing reaction to post
     *
     */
    public static function removeReactionToPost(Request $request, $reactionId)
    {
        // Make sure reaction ID is provided
        if (! $reactionId) {
            return ResponseHelper::sendError('Reaction ID is required.', null, 400);
        }

        // Decode the token from the Authorization header
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Find the reaction made by this user on the given post
        $reaction = Reaction::where('id', $reactionId)
            ->where('reaction_user_id', $user->id)
            ->first();

        // Check if the reaction exists before proceeding
        if (! $reaction) {
            return ResponseHelper::sendError('Reaction not found.', null, 404);
        }

        // Try to find a related notification for this reaction
        $notification = Notification::where('notification_post_id', $reaction->reaction_post_id)
            ->where('notification_user_id', $reaction->reaction_user_id)
            ->where('notification_type', 'reaction')
            ->first();

        // Fire events before deletion so the frontend receives the data
        broadcast(new ReactionRemoved((object)['post_id' => $postId]));

        if ($notification) {
            event(new NotificationRemoved($notification));
            $notification->delete();
        }

        // Delete the reaction record
        $reaction->delete();

        // Return consistent API response
        return ResponseHelper::sendSuccess(null, 'Reaction removed successfully.', 200);
    }
}
