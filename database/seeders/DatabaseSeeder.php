<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Message;
use App\Models\Chatroom;
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
        $users = User::factory(15)->create([
            'user_password' => bcrypt('password'),
        ]);

        // Display seeded user credentials
        $this->command->info("\n--- DEMO ACCOUNTS ---");
        foreach ($users as $user) {
            $this->command->info("{$user->user_email} | password: password");
        }
        $this->command->info("----------------------\n");

        // --- ORIGINAL POSTS ---
        $this->command->info("Creating base posts...");
        $users->each(function ($user) {
            Post::factory(rand(3, 6))->create([
                'post_user_id' => $user->id,
                'post_is_shared' => false,
                'post_share_id' => null,
            ]);
        });

        // --- SHARED POSTS ---
        $this->command->info("Creating shared posts...");
        $allPosts = Post::all();
        foreach ($allPosts->random(min(5, $allPosts->count())) as $originalPost) {
            $sharer = $users->where('id', '!=', $originalPost->post_user_id)->random();

            // Create a share record first
            $share = Share::factory()->create([
                'share_user_id' => $sharer->id,
                'share_original_post_id' => $originalPost->id,
            ]);

            // Then create the actual shared post that references the share
            Post::factory()->create([
                'post_user_id' => $sharer->id,
                'post_content' => 'Check this out! 🔁',
                'post_is_shared' => true,
                'post_share_id' => $share->id,
            ]);

            // Notify the original post owner
            if ($sharer->id !== $originalPost->post_user_id) {
                Notification::factory()->create([
                    'notification_user_id' => $originalPost->post_user_id,
                    'notification_post_id' => $originalPost->id,
                    'notification_type' => 'share',
                    'notification_message' => "{$sharer->user_fname} shared your post",
                ]);
            }
        }

        // --- COMMENTS & REACTIONS ---
        $this->command->info("Creating comments and reactions...");
        foreach (Post::inRandomOrder()->take(20)->get() as $post) {
            $commenter = $users->random();
            $comment = Comment::factory()->create([
                'comment_post_id' => $post->id,
                'comment_user_id' => $commenter->id,
            ]);

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

            if ($reactor->id !== $post->post_user_id) {
                Notification::factory()->create([
                    'notification_user_id' => $post->post_user_id,
                    'notification_post_id' => $post->id,
                    'notification_type' => 'reaction',
                    'notification_message' => "{$reactor->user_fname} reacted to your post",
                ]);
            }
        }

        // --- MESSAGES ---
        $this->command->info("Creating chatrooms and messages...");

        // Create around 10 chatrooms with messages
        for ($i = 0; $i < 10; $i++) {
            $userOne = $users->random();
            $userTwo = $users->where('id', '!=', $userOne->id)->random();

            // Create chatroom for these two users
            $chatroom = Chatroom::create([
                'cr_user_one_id' => $userOne->id,
                'cr_user_two_id' => $userTwo->id,
            ]);

            // Create 10–15 messages alternating between the two users
            for ($m = 0; $m < rand(10, 15); $m++) {
                $isUserOneSender = fake()->boolean(50);

                Message::factory()->create([
                    'message_chatroom_id' => $chatroom->id,
                    'message_sender_id'   => $isUserOneSender ? $userOne->id : $userTwo->id,
                    'message_receiver_id' => $isUserOneSender ? $userTwo->id : $userOne->id,
                ]);
            }
        }

        // --- EXTRA NOTIFICATIONS (UNREAD) ---
        Notification::factory(10)->create([
            'notification_user_id' => $users->random()->id,
            'notification_read' => false,
        ]);

        // --- DATA INTEGRITY CLEANUP ---
        $this->command->info("Running integrity cleanup...");

        // Remove invalid shared posts
        Post::where('post_is_shared', true)
            ->whereNull('post_share_id')
            ->delete();

        // Remove orphan shares with missing posts
        Share::doesntHave('originalPost')->delete();

        $this->command->info("\n✅ Database seeding completed successfully and verified for data integrity.\n");
    }
}
