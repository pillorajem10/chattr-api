<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Post;

/**
 * Factory for generating fake share data.
 *
 * This factory ensures that:
 * - Each share belongs to a valid user.
 * - Each share references an existing original post.
 * - Shared posts have proper timestamps for realistic seed data.
 */
class ShareFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'share_user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'share_original_post_id' => Post::inRandomOrder()->first()->id ?? Post::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
