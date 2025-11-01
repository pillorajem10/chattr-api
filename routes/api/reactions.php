<?php

use App\Http\Controllers\ReactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Reaction Routes
|--------------------------------------------------------------------------
| Handles reaction-related endpoints such as creating and retrieving reactions.
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/{postId}', [ReactionController::class, 'reactToPost']);
    Route::get('/{postId}', [ReactionController::class, 'getReactionsForPost']);
    Route::delete('/{reactionId}', [ReactionController::class, 'removeReactionToPost']);
});
