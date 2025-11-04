<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for generating fake comment data.
 *
 * This factory creates sample comments linked to random users and posts.
 * It ensures each comment has valid relationships and realistic content.
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'comment_post_id' => Post::inRandomOrder()->first()->id ?? Post::factory(),
            'comment_user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'comment_content' => $this->faker->sentence(12),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
