<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| This is the entry point for all API route groups.
| Each module has its own dedicated route file inside /routes/api.
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(base_path('routes/api/auth.php'));
Route::prefix('posts')->group(base_path('routes/api/posts.php'));
Route::prefix('reactions')->group(base_path('routes/api/reactions.php'));
Route::prefix('comments')->group(base_path('routes/api/comments.php'));
Route::prefix('shares')->group(base_path('routes/api/shares.php'));