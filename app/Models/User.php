<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;

    protected $table = 'tbl_users';

    protected $fillable = [
        'user_fname',
        'user_lname',
        'user_email',
        'user_password',
        'user_bio',
    ];

    protected $hidden = [
        'user_password',
    ];

    public $timestamps = true;

    /**
     * Get all posts created by this user.
     *
     * This defines the one-to-many relationship between
     * the user and their posts.
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'post_user_id');
    }

    /**
     * Get all reactions made by this user.
     *
     * This defines the one-to-many relationship between
     * the user and their reactions.
     */
    public function reactions()
    {
        return $this->hasMany(Reaction::class, 'reaction_user_id');
    }

    /**
     * Get all messages sent by this user.
     *
     * This defines the one-to-many relationship between
     * the user and the messages they have sent.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'message_sender_id');
    }

    /**
     * Get all messages received by this user.
     *
     * This defines the one-to-many relationship between
     * the user and the messages they have received.
     */

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'message_receiver_id');
    }

    /**
     * Get all comments made by this user.
     *
     * This defines the one-to-many relationship between
     * the user and their comments.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'comment_user_id');
    }
}
