<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Post;

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
