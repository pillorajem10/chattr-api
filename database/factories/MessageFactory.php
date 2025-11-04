<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Chatroom;

/**
 * Factory for generating fake private messages between users.
 *
 * This factory ensures that:
 * - Each message belongs to one valid chatroom.
 * - Sender and receiver are the two participants in that chatroom.
 * - Each message randomly alternates between sender/receiver.
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
        // Pick an existing chatroom or create one with two users
        $chatroom = Chatroom::inRandomOrder()->first();

        if (!$chatroom) {
            $userOne = User::factory()->create();
            $userTwo = User::factory()->create();

            $chatroom = Chatroom::create([
                'cr_user_one_id' => $userOne->id,
                'cr_user_two_id' => $userTwo->id,
            ]);
        }

        // Pick sender and receiver based on chatroom participants
        $isUserOneSender = $this->faker->boolean(50);

        $senderId = $isUserOneSender
            ? $chatroom->cr_user_one_id
            : $chatroom->cr_user_two_id;

        $receiverId = $isUserOneSender
            ? $chatroom->cr_user_two_id
            : $chatroom->cr_user_one_id;

        return [
            'message_sender_id'   => $senderId,
            'message_receiver_id' => $receiverId,
            'message_chatroom_id' => $chatroom->id,
            'message_content'     => $this->faker->realTextBetween(20, 120),
            'message_read'        => $this->faker->boolean(30),
            'created_at'          => $this->faker->dateTimeBetween('-3 days', 'now'),
            'updated_at'          => now(),
        ];
    }
}
