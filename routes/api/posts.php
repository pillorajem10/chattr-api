<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Post Routes
|--------------------------------------------------------------------------
| Handles post-related endpoints such as creating and retrieving posts.
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/', [PostController::class, 'getAllPosts']);
    Route::post('/', [PostController::class, 'createPost']);
    Route::get('/{postId}', [PostController::class, 'getPostById']);
    Route::delete('/{postId}', [PostController::class, 'deletePostById']);
});
