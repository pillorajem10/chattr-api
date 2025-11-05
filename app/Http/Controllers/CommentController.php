<?php

namespace App\Http\Controllers;

use App\Events\CommentCreated;
use App\Events\CommentRemoved;
use App\Events\NotificationCreated;
use App\Helpers\ResponseHelper;
use App\Helpers\TokenHelper;
use App\Http\Validations\CommentValidationMessages;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * ==========================================================
 * Controller: CommentController
 * ----------------------------------------------------------
 * Handles all comment-related actions for posts, including:
 * - Adding comments
 * - Retrieving comments
 * - Removing comments
 *
 * Integrations:
 * - TokenHelper: For decoding authenticated user tokens.
 * - ResponseHelper: For consistent JSON responses.
 * - Laravel Events: For real-time broadcasting (CommentCreated,
 *   CommentRemoved, NotificationCreated).
 * ==========================================================
 */
class CommentController extends Controller
{
    /**
     * Add a comment to a post.
     *
     * Validates input, ensures the post exists,
     * creates a new comment, notifies the post owner,
     * and broadcasts real-time updates.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function commentOnPost(Request $request, $postId)
    {
        // Decode the token from the Authorization header
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Verify that the post exists
        $post = Post::find($postId);
        if (! $post) {
            return ResponseHelper::sendError('Post not found.', null, 404);
        }

        // Validate request data
        $validator = Validator::make(
            $request->all(),
            [
                'comment_content' => 'required|string|max:500',
            ],
            CommentValidationMessages::create()
        );

        // Return the first validation error if validation fails
        if ($validator->fails()) {
            $firstError = collect($validator->errors()->all())->first();
            return ResponseHelper::sendError($firstError, null, 422);
        }

        // Create a new comment
        $comment = Comment::create([
            'comment_post_id' => $postId,
            'comment_user_id' => $user->id,
            'comment_content' => $request->comment_content,
        ]);

        // Broadcast real-time "comment created" event
        event(new CommentCreated($comment));

        // Notify the post owner (skip if the commenter is the owner)
        if ($post->user->id !== $user->id) {
            $notification = Notification::create([
                'notification_user_id' => $post->user->id,
                'notification_type'    => 'comment',
                'notification_post_id' => $postId,
                'notification_message' => "{$user->user_fname} {$user->user_lname} commented on your post.",
            ]);

            // Broadcast real-time "notification created" event
            event(new NotificationCreated($notification));
        }

        // Return success response
        return ResponseHelper::sendSuccess($comment, 'Comment added successfully.', 201);
    }

    /**
     * Retrieve all comments for a specific post.
     *
     * Fetches comments with their associated user data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCommentsForPost(Request $request, $postId)
    {
        // Fetch comments with associated users
        $comments = Comment::where('comment_post_id', $postId)
            ->with('user')
            ->get();

        // Return success response
        return ResponseHelper::sendSuccess($comments, 'Comments retrieved successfully.', 200);
    }

    /**
     * Remove a comment from a post.
     *
     * Validates ownership, deletes the comment,
     * and broadcasts a real-time removal event.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $commentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeCommentFromPost(Request $request, $commentId)
    {
        // Decode the token from the Authorization header
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Verify that the comment exists
        $comment = Comment::find($commentId);
        if (! $comment) {
            return ResponseHelper::sendError('Comment not found.', null, 404);
        }

        // Ensure the authenticated user owns the comment
        if ($comment->comment_user_id !== $user->id) {
            return ResponseHelper::sendError('You are not authorized to delete this comment.', null, 403);
        }

        // Delete the comment
        $comment->delete();

        // Broadcast real-time "comment removed" event
        event(new CommentRemoved($commentId));

        // Return success response
        return ResponseHelper::sendSuccess(null, 'Comment removed successfully.', 200);
    }
}
