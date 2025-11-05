<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Helpers\TokenHelper;
use App\Http\Validations\PostValidationMessages;
use App\Models\Post;
use App\Models\Share;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * ==========================================================
 * Controller: PostController
 * ----------------------------------------------------------
 * Manages all post-related operations, including:
 * - Retrieving posts with pagination
 * - Creating posts
 * - Fetching post details by ID
 * - Deleting posts (including shared posts)
 *
 * Integrations:
 * - TokenHelper: Authenticates user via bearer token.
 * - ResponseHelper: Formats API responses consistently.
 * - Validation classes: Provides structured validation messages.
 * ==========================================================
 */
class PostController extends Controller
{
    /**
     * Retrieve all posts with pagination and related data.
     *
     * Includes:
     * - User details
     * - Original post if shared
     * - Reaction, comment, and share counts
     * - Whether the authenticated user has liked the post
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllPosts(Request $request)
    {
        // Decode token to identify the current user
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Base query with relationships and counts
        $postsQuery = Post::with([
                'user',
                'share.originalPost.user',
            ])
            ->withCount([
                'reactions as likesCount' => fn($query) => $query->where('reaction_type', 'like'),
                'comments as commentCount' => fn($query) => $query->whereNotNull('comment_post_id'),
                'shares as shareCount' => fn($query) => $query->whereNotNull('share_original_post_id'),
            ])
            ->orderBy('created_at', 'desc');

        // Pagination setup
        $pageIndex = (int) $request->query('pageIndex', 1);
        $pageSize  = (int) $request->query('pageSize', 10);

        $totalRecords = $postsQuery->count();
        $totalPages   = ceil($totalRecords / $pageSize);

        // Fetch paginated posts
        $posts = $postsQuery
            ->skip(($pageIndex - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->map(function ($post) use ($user) {
                // Include shared post details if applicable
                if ($post->post_is_shared && $post->post_share_id) {
                    $share = $post->share;
                    if ($share && $share->originalPost) {
                        $post->original_post = $share->originalPost;
                    }
                }

                // Determine if current user liked this post
                $userReaction = $post->reactions()
                    ->where('reaction_user_id', $user->id)
                    ->where('reaction_type', 'like')
                    ->first();

                $post->likedByUser = (bool) $userReaction;
                $post->user_reaction_id = $userReaction?->id ?? null;

                return $post;
            });

        // Return formatted paginated response
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
     * Validates input data, saves it to the database,
     * and returns the newly created post.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPost(Request $request)
    {
        // Decode the token from the Authorization header
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Validate input
        $validator = Validator::make(
            $request->all(),
            ['post_content' => 'required|string|max:1000'],
            PostValidationMessages::create()
        );

        // Return validation error if any
        if ($validator->fails()) {
            $firstError = collect($validator->errors()->all())->first();
            return ResponseHelper::sendError($firstError, null, 422);
        }

        // Create new post
        $post = Post::create([
            'post_content' => $request->post_content,
            'post_user_id' => $user->id,
        ]);

        // Return success response
        return ResponseHelper::sendSuccess($post, 'Post created successfully.', 201);
    }

    /**
     * Retrieve a post by its ID, including user details.
     *
     * @param  int  $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public static function getPostById($postId)
    {
        // Validate post ID
        if (! $postId) {
            return ResponseHelper::sendError('Post ID is required.', null, 400);
        }

        // Fetch post with user relationship
        $post = Post::with('user')->find($postId);

        // Return error if not found
        if (! $post) {
            return ResponseHelper::sendError('Post not found.', null, 404);
        }

        // Return success response
        return ResponseHelper::sendSuccess($post, 'Post retrieved successfully.', 200);
    }

    /**
     * Delete a post by its ID.
     *
     * If the post is shared, also deletes the associated share record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public static function deletePostById(Request $request, $postId)
    {
        // Validate post ID
        if (! $postId) {
            return ResponseHelper::sendError('Post ID is required.', null, 400);
        }

        // Decode token for authentication
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Fetch post record
        $post = Post::find($postId);

        // Validate post existence
        if (! $post) {
            return ResponseHelper::sendError('Post not found.', null, 404);
        }

        // Ensure the authenticated user owns the post
        if ($post->post_user_id !== $user->id) {
            return ResponseHelper::sendError('Unauthorized to delete this post.', null, 403);
        }

        // If shared post, delete associated share record
        if ($post->post_is_shared) {
            $share = Share::where('share_post_id', $postId)
                ->where('share_user_id', $user->id)
                ->first();

            if ($share) {
                $share->delete();
            }
        }

        // Delete post
        $post->delete();

        // Return success response
        return ResponseHelper::sendSuccess(null, 'Post deleted successfully.', 200);
    }
}
