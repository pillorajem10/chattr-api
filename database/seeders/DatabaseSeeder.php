<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Comment;
use App\Models\Reaction;
use App\Models\Share;

/**
 * DatabaseSeeder
 *
 * Seeds the database with realistic interconnected data
 * including Users, Posts, Comments, Reactions, Shares,
 * Messages, and Notifications.
 *
 * Each seeded user has a known password: "password"
 * This allows easy login for demonstration or review purposes.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- USERS ---
        $users = User::factory(10)->create([
            'user_password' => bcrypt('password'),
        ]);

        // Display seeded user credentials
        $this->command->info("\n--- DEMO ACCOUNTS ---");
        foreach ($users as $user) {
            $this->command->info("{$user->user_email} | password: password");
        }
        $this->command->info("----------------------\n");

        // --- POSTS ---
        $users->each(function ($user) {
            Post::factory(rand(2, 5))->create(['post_user_id' => $user->id]);
        });

        // --- COMMENTS, REACTIONS, AND COMMENT NOTIFICATIONS ---
        foreach (Post::inRandomOrder()->take(20)->get() as $post) {
            $commenter = $users->random();
            $comment = Comment::factory()->create([
                'comment_post_id' => $post->id,
                'comment_user_id' => $commenter->id,
            ]);

            // Notification for comment
            if ($commenter->id !== $post->post_user_id) {
                Notification::factory()->create([
                    'notification_user_id' => $post->post_user_id,
                    'notification_post_id' => $post->id,
                    'notification_type' => 'comment',
                    'notification_message' => "{$commenter->user_fname} commented on your post",
                ]);
            }

            // --- REACTIONS ---
            $reactor = $users->random();
            $reaction = Reaction::factory()->create([
                'reaction_post_id' => $post->id,
                'reaction_user_id' => $reactor->id,
            ]);

            // Notification for reaction
            if ($reactor->id !== $post->post_user_id) {
                Notification::factory()->create([
                    'notification_user_id' => $post->post_user_id,
                    'notification_post_id' => $post->id,
                    'notification_type' => 'reaction',
                    'notification_message' => "{$reactor->user_fname} reacted to your post",
                ]);
            }
        }

        // --- SHARES ---
        foreach (Post::inRandomOrder()->take(5)->get() as $post) {
            $sharer = $users->random();
            $share = Share::factory()->create([
                'share_user_id' => $sharer->id,
                'share_original_post_id' => $post->id,
            ]);

            // Notification for share
            if ($sharer->id !== $post->post_user_id) {
                Notification::factory()->create([
                    'notification_user_id' => $post->post_user_id,
                    'notification_post_id' => $post->id,
                    'notification_type' => 'share',
                    'notification_message' => "{$sharer->user_fname} shared your post",
                ]);
            }
        }

        // --- MESSAGES ---
        for ($i = 0; $i < 30; $i++) {
            $sender = $users->random();
            $receiver = $users->where('id', '!=', $sender->id)->random();
            Message::factory()->create([
                'message_sender_id' => $sender->id,
                'message_receiver_id' => $receiver->id,
            ]);
        }

        // --- EXTRA NOTIFICATIONS (UNREAD) ---
        Notification::factory(10)->create([
            'notification_user_id' => $users->random()->id,
            'notification_read' => false,
        ]);

        $this->command->info("\nDatabase seeding completed successfully.");
    }
}
