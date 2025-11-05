<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Helpers\ResponseHelper;
use App\Helpers\TokenHelper;
use App\Http\Validations\ShareValidationMessages;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Share;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * ==========================================================
 * Controller: ShareController
 * ----------------------------------------------------------
 * Handles post-sharing functionality, including:
 * - Creating a share record
 * - Generating a shared post
 * - Sending notifications to the original post owner
 * - Broadcasting real-time updates via events
 *
 * Integrations:
 * - TokenHelper: Authenticates user via bearer token.
 * - ResponseHelper: Formats standardized API responses.
 * - NotificationCreated Event: Notifies users in real time.
 * ==========================================================
 */
class ShareController extends Controller
{
    /**
     * Share an existing post.
     *
     * Validates the request input, creates both a "share" record
     * and a new post marked as shared, and notifies the original
     * post owner in real time.
     *
     * Flow:
     * 1. Validate caption input (optional).
     * 2. Confirm that the original post exists.
     * 3. Create a share record and a new "shared post."
     * 4. Notify the original post owner (except self-shares).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int                       $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function sharePost(Request $request, $postId)
    {
        // Validate request input
        $validator = Validator::make(
            $request->all(),
            ['share_caption' => 'nullable|string|max:1000'],
            ShareValidationMessages::share()
        );

        if ($validator->fails()) {
            return ResponseHelper::validationError($validator->errors());
        }

        // Decode user from token
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Check if the original post exists
        $originalPost = Post::find($postId);
        if (! $originalPost) {
            return ResponseHelper::sendError('Original post not found.', null, 404);
        }

        // Create a new share record
        $share = Share::create([
            'share_user_id'         => $user->id,
            'share_original_post_id'=> $originalPost->id,
        ]);

        // Create a new post entry as a shared post
        $sharedPost = Post::create([
            'post_user_id'    => $user->id,
            'post_content'    => $request->share_caption,
            'post_is_shared'  => true,
            'post_share_id'   => $share->id,
        ]);

        // Notify the original post owner (skip self-shares)
        if ($originalPost->post_user_id !== $user->id) {
            $notification = Notification::create([
                'notification_user_id' => $originalPost->post_user_id,
                'notification_type'    => 'share',
                'notification_post_id' => $originalPost->id,
                'notification_message' => "{$user->user_fname} {$user->user_lname} shared your post.",
            ]);

            // Broadcast real-time notification
            event(new NotificationCreated($notification));
        }

        // Return success response
        return ResponseHelper::sendSuccess($share, 'Post shared successfully.', 201);
    }
}
