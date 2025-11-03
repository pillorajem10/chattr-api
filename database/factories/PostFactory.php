<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Share;

/**
 * Factory for generating fake post data.
 *
 * Ensures:
 * - Each post is created by a valid user.
 * - If post_is_shared is true, a valid Share record is created and linked.
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $isShared = $this->faker->boolean(30); // 30% of posts are shared

        // Always make sure we have a user
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        $shareId = null;

        // If shared, ensure a valid Share record exists
        if ($isShared) {
            $originalPost = self::getOriginalPost();
            $share = Share::factory()->create([
                'share_user_id' => $user->id,
                'share_original_post_id' => $originalPost->id,
            ]);
            $shareId = $share->id;
        }

        return [
            'post_user_id'   => $user->id,
            'post_content'   => $this->faker->sentence(10),
            'post_is_shared' => $isShared,
            'post_share_id'  => $shareId,
            'created_at'     => now(),
            'updated_at'     => now(),
        ];
    }

    /**
     * Helper: Fetch a random existing post or create one.
     */
    private static function getOriginalPost()
    {
        return \App\Models\Post::inRandomOrder()->first() ?? \App\Models\Post::factory()->create();
    }
}
