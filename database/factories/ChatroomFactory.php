<?php

namespace Database\Factories;

use App\Models\Chatroom;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for generating fake chatroom data.
 *
 * This factory creates sample chatroom linked to random users.
 * It ensures each chatroom has valid relationships and realistic content.
 */
class ChatroomFactory extends Factory
{
    protected $model = Chatroom::class;

    public function definition(): array
    {
        // Make sure user_one and user_two are different
        $userOne = User::inRandomOrder()->first() ?? User::factory()->create();
        $userTwo = User::where('id', '!=', $userOne->id)->inRandomOrder()->first() ?? User::factory()->create();

        return [
            'cr_user_one_id' => $userOne->id,
            'cr_user_two_id' => $userTwo->id,
        ];
    }
}
