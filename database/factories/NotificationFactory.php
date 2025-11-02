<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Post;

/**
 * Factory for generating fake notification data.
 *
 * This factory ensures each notification:
 * - Belongs to a valid user.
 * - Is linked to an existing post.
 * - Has a realistic notification type and message.
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'notification_user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'notification_post_id' => Post::inRandomOrder()->first()->id ?? Post::factory(),
            'notification_type' => $this->faker->randomElement(['reaction', 'comment', 'share']),
            'notification_message' => $this->faker->sentence(6),
            'notification_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
