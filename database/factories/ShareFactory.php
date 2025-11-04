<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for generating fake share data.
 *
 * Ensures:
 * - Each share belongs to a valid user.
 * - Each share references an existing original post.
 */
class ShareFactory extends Factory
{
    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        $originalPost = Post::inRandomOrder()->first() ?? Post::factory()->create();

        return [
            'share_user_id' => $user->id,
            'share_original_post_id' => $originalPost->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
