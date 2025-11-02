<?php

use App\Http\Controllers\ShareController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Share Routes
|--------------------------------------------------------------------------
| Handles post-sharing related endpoints such as creating and retrieving shares.
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/{postId}', [ShareController::class, 'sharePost']);
});
