<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Share;

/**
 * Factory for generating fake post data.
 *
 * This factory ensures that:
 * - Each post is created by a valid user.
 * - Shared posts optionally reference an existing Share record.
 * - The post_share_id is only set when post_is_shared is true.
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $isShared = $this->faker->boolean(30);

        return [
            'post_user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'post_content' => $this->faker->sentence(10),
            'post_is_shared' => $isShared,
            'post_share_id' => $isShared ? (Share::inRandomOrder()->first()->id ?? null) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
