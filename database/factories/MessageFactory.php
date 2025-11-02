<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * Factory for generating fake private messages between users.
 *
 * This factory ensures that:
 * - Each message has a valid sender and receiver.
 * - The sender and receiver are different users.
 * - Some messages are randomly marked as read.
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        // Pick two distinct users for sender and receiver
        $sender = User::inRandomOrder()->first() ?? User::factory()->create();
        $receiver = User::inRandomOrder()
            ->where('id', '!=', $sender->id)
            ->first() ?? User::factory()->create();

        return [
            'message_sender_id' => $sender->id,
            'message_receiver_id' => $receiver->id,
            'message_content' => $this->faker->sentence(8),
            'message_read' => $this->faker->boolean(30),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
