<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Events\NotificationRemoved;
use App\Helpers\ResponseHelper;
use App\Helpers\TokenHelper;
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
     * A new "Post" will be created with post_is_shared set to true.
     */
    public function sharePost(Request $request, $postId)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            "share_caption" => "nullable|string|max:1000",
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        $user = TokenHelper::decodeToken($request->header('Authorization'));
        $originalPost = Post::find($postId);

        // Check if the original post exists
        if (!$originalPost) {
            return ResponseHelper::sendError('Original post not found.', null, 404);
        }

        // Create a share record
        $share = Share::create([
            'share_user_id' => $user->id,
            'share_original_post_id' => $originalPost->id,
        ]);

        // Create a new post as a shared post
        $sharedPost = Post::create([
            'post_user_id' => $user->id,
            'post_content' => $request->share_caption,
            'post_is_shared' => true,
            'post_share_id' => $share->id,
        ]);

        // Create a notification for the original post owner
        if ($originalPost->post_user_id !== $user->id) {
            $notification = Notification::create([
                'notification_user_id' => $originalPost->post_user_id,
                'notification_type' => 'share',
                'notification_post_id' => $originalPost->id,
                'notification_message' => "{$user->user_fname} {$user->user_lname} shared your post.",
            ]);

            // Broadcast the notification event
            event(new NotificationCreated($notification));
        }

        return ResponseHelper::sendSuccess($share, 'Post shared successfully.', 201);
    }
}
