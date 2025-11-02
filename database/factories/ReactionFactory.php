<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Post;

/**
 * Factory for generating fake reaction data.
 *
 * This factory ensures that:
 * - Each reaction is associated with a valid user and post.
 * - Reaction types are varied for realistic social interactions.
 */
class ReactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'reaction_post_id' => Post::inRandomOrder()->first()->id ?? Post::factory(),
            'reaction_user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'reaction_type' => $this->faker->randomElement(['like', 'love', 'haha', 'wow', 'sad', 'angry']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
