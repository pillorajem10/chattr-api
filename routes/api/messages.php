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
    Route::get('/', [MessageController::class, 'getUserChatrooms']);
    Route::get('/{chatroomId}', [MessageController::class, 'getConversation']);
    Route::post('/', [MessageController::class, 'sendMessage']);
    Route::patch('/{chatroomId}/mark-read', [MessageController::class, 'markConversationAsRead']);
    Route::post('/create-chatroom', [MessageController::class, 'createChatroom']);
});
