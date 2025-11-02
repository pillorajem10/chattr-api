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
     * Get all users except the authenticated user
     * 
     * with pagination response helper
     * 
     * @param Request $request
     */
    public function getAllPosts(Request $request)
    {
        // Get authenticated user ID
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Fetch posts
        $postsQuery = Post::with('user')->orderBy('created_at', 'desc');

        // Pagination parameters
        $pageIndex = (int) $request->query('pageIndex', 1);
        $pageSize  = (int) $request->query('pageSize', 10);

        // Get total records count
        $totalRecords = $postsQuery->count();

        // Calculate total pages
        $totalPages = ceil($totalRecords / $pageSize);

        // Fetch paginated posts
        $posts = $postsQuery->skip(($pageIndex - 1) * $pageSize)
                            ->take($pageSize)
                            ->get();

        // Return paginated response
        return ResponseHelper::sendPaginatedResponse(
            $posts,
            $pageIndex,
            $pageSize,
            $totalPages,
            $totalRecords
        );
    }

    /**
     * Create a new post.
     *
     * Validates input data and creates a new post in the database.
     */
    public function createPost(Request $request)
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
