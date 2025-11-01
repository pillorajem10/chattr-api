<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Helpers\TokenHelper;
use App\Models\Post;
use App\Models\Share;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Retrieve all posts.
     *
     * Fetches all posts from the database and returns them in a structured response.
     */
    public function posts(Request $request)
    {
        // Pagination parameters
        $pageIndex = $request->query('pageIndex', 1);
        $pageSize = $request->query('pageSize', 10);

        // Fetch paginated posts with the owner details
        $posts = Post::with('user')->paginate($pageSize, ['*'], 'page', $pageIndex);

        // return response
        return ResponseHelper::sendSuccess($posts, 'Posts retrieved successfully.', 200);
    }

    /**
     * Create a new post.
     *
     * Validates input data and creates a new post in the database.
     */
    public function create(Request $request)
    {
        // Decode the token from the Authorization header
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'post_content' => 'required|string|max:1000',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            // get the first array for the message
            $firstError = collect($validator->errors()->all())->first();

            // Return the first validation error
            return ResponseHelper::sendError($firstError, null, 422);
        };

        // Create new post
        $post = Post::create([
            'post_content' => $request->post_content,
            'post_user_id' => $user->id,
        ]);

        // Return response
        return ResponseHelper::sendSuccess($post, 'Post created successfully.', 201);
    }

    /**
     * Get Post By ID params
     *
     * Fetch its user details as well
     */
    public static function getPostById($postId)
    {
        // Validate postId
        if (!$postId) {
            return ResponseHelper::sendError('Post ID is required.', null, 400);
        }

        // Fetch post with user details
        $post = Post::with('user')->find($postId);

        // Check if post exists
        if (!$post) {
            return ResponseHelper::sendError('Post not found.', null, 404);
        }

        // Return response
        return ResponseHelper::sendSuccess($post, 'Post retrieved successfully.', 200);
    }

    /**
     * Delete Post By ID
     * 
     * If its a shared post the share record will be deleted as well
     */
    public static function deletePostById(Request $request, $postId)
    {
        // Validate postId
        if (!$postId) {
            return ResponseHelper::sendError('Post ID is required.', null, 400);
        }

        // Decode the token from the Authorization header
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Fetch post
        $post = Post::find($postId);

        // Check if post exists
        if (!$post) {
            return ResponseHelper::sendError('Post not found.', null, 404);
        }

        // Check if the authenticated user is the owner of the post
        if ($post->post_user_id !== $user->id) {
            return ResponseHelper::sendError('Unauthorized to delete this post.', null, 403);
        }

        // if its a shared post, delete the related share record
        if ($post->post_is_shared) {
            $share = Share::where('share_post_id', $postId)
                ->where('share_user_id', $user->id)
                ->first();
            if ($share) {
                $share->delete();
            }
        }

        // Delete the post
        $post->delete();

        // Return response
        return ResponseHelper::sendSuccess(null, 'Post deleted successfully.', 200);
    }
}
