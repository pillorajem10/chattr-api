<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Messages Routes
|--------------------------------------------------------------------------
| Handles message-related endpoints such as sending and retrieving messages.
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/{receiverId}', [MessageController::class, 'getConversation']);
    Route::post('/', [MessageController::class, 'sendMessage']);
    Route::post('/mark-read/{senderId}', [MessageController::class, 'markConversationAsRead']);
});
