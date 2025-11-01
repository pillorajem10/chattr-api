<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Events\NotificationRemoved;
use App\Models\Post;
use App\Models\Share;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShareController extends Controller
{
    /**
     * Share a post.
     *
     * Validates input data and creates a new share in the database.
     * Notifies the post owner about the new share.
     * Real-time broadcasting of the new share event.
     * 
     * A new post will be created post_is_shared = true, post_shared_id = original post id
     */
    public function sharePost(Request $request, $postId)
    {
        // Decode the token from the Authorization header
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // validate incoming request data
        $validator = Validator::make($request->all(), [
            'share_caption' => 'nullable|string|max:255',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            // get the first array for the message
            $firstError = collect($validator->errors()->all())->first();

            // Return the first validation error
            return ResponseHelper::sendError($firstError, null, 422);
        };

        // Verify that the post exists
        $post = Post::find($postId);
        if (!$post) {
            return ResponseHelper::sendError('Post not found.', null, 404);
        }

        // Check if the user has already shared the post
        $existingShare = Share::where('share_post_id', $postId)
            ->where('share_user_id', $user->id)
            ->first();

        if ($existingShare) {
            return ResponseHelper::sendError('You have already shared this post.', null, 409);
        }

        // Create new share
        $share = Share::create([
            'share_post_id' => $postId,
            'share_user_id' => $user->id,
            'share_caption' => $request->input('share_caption', ''),
        ]);

        // Fire the real-time share created event
        event(new ShareCreated($share));

        // Notify the post owner about the new share
        if ($post->user->id !== $user->id) {
            $notification = Notification::create([
                'notification_user_id' => $post->user->id,
                'notification_type'    => 'share',
                'notification_post_id' => $postId,
                'notification_message' => "{$user->user_fname} {$user->user_lname} shared your post.",
            ]);

            // Fire the real-time notification created event
            event(new NotificationCreated($notification));
        }

        // Return response
        return ResponseHelper::sendSuccess($share, 'Post shared successfully.', 201);
    }
}
