<?php

use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Comment Routes
|--------------------------------------------------------------------------
| Handles comment-related endpoints such as creating and retrieving comments.
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/{postId}', [CommentController::class, 'commentOnPost']);
    Route::get('/{postId}', [CommentController::class, 'getCommentsForPost']);
    Route::delete('/{commentId}', [CommentController::class, 'removeCommentFromPost']);
});