<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Events\NotificationRemoved;
use App\Events\CommentCreated;
use App\Events\CommentRemoved;
use App\Helpers\ResponseHelper;
use App\Helpers\TokenHelper;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Notification;
use App\Http\Validations\CommentValidationMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Add a comment to a post.
     *
     * Validates input data and creates a new comment in the database.
     * Notifies the post owner about the new comment.
     * Real-time broadcasting of the new comment event.
     * 
     */
    public function commentOnPost(Request $request, $postId)
    {
        // Decode the token from the Authorization header
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Verify that the post exists
        $post = Post::find($postId);
        if (!$post) {
            return ResponseHelper::sendError('Post not found.', null, 404);
        }

        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'comment_content' => 'required|string|max:500',
        ], CommentValidationMessages::create());

        // Return validation errors if any
        if ($validator->fails()) {
            // get the first array for the message
            $firstError = collect($validator->errors()->all())->first();

            // Return the first validation error
            return ResponseHelper::sendError($firstError, null, 422);
        };

        // Create new comment
        $comment = Comment::create([
            'comment_post_id' => $postId,
            'comment_user_id' => $user->id,
            'comment_content' => $request->comment_content,
        ]);

        // Fire the real-time comment created event
        event(new CommentCreated($comment));

        // Notify the post owner (but skip self-comments)
        if ($post->user->id !== $user->id) {
            $notification = Notification::create([
                'notification_user_id' => $post->user->id,
                'notification_type'    => 'comment',
                'notification_post_id' => $postId,
                'notification_message' => "{$user->user_fname} {$user->user_lname} commented on your post.",
            ]);

            // Fire the real-time notification created event
            event(new NotificationCreated($notification));
        }

        // Return response
        return ResponseHelper::sendSuccess($comment, 'Comment added successfully.', 201);
    }

    /**
     * Get comments for a post.
     * 
     */
    public function getCommentsForPost(Request $request, $postId)
    {
        // Fetch comments for the post
        $comments = Comment::where('comment_post_id', $postId)
            ->with('user')
            ->get();

        // Return response
        return ResponseHelper::sendSuccess($comments, 'Comments retrieved successfully.', 200);
    }

    /**
     * Remove a comment from a post.
     * 
     * Real-time broadcasting of the comment removed event.
     */
    public function removeCommentFromPost(Request $request, $commentId)
    {
        // Decode the token from the Authorization header
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Verify that the comment exists
        $comment = Comment::find($commentId);
        if (!$comment) {
            return ResponseHelper::sendError('Comment not found.', null, 404);
        }

        // Check if the authenticated user is the owner of the comment
        if ($comment->comment_user_id !== $user->id) {
            return ResponseHelper::sendError('You are not authorized to delete this comment.', null, 403);
        }

        // Delete the comment
        $comment->delete();

        // Fire the real-time comment removed event
        event(new CommentRemoved($commentId));

        // Return response
        return ResponseHelper::sendSuccess(null, 'Comment removed successfully.', 200);
    }
}
