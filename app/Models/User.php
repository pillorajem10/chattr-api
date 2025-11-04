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
     * Defines a one-to-many relationship between
     * User and Post models.
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'post_user_id');
    }

    /**
     * Get all reactions made by this user.
     *
     * Defines a one-to-many relationship between
     * User and Reaction models.
     */
    public function reactions()
    {
        return $this->hasMany(Reaction::class, 'reaction_user_id');
    }

    /**
     * Get all messages sent by this user.
     *
     * Defines a one-to-many relationship between
     * User and Message models (as sender).
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'message_sender_id');
    }

    /**
     * Get all messages received by this user.
     *
     * Defines a one-to-many relationship between
     * User and Message models (as receiver).
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'message_receiver_id');
    }

    /**
     * Get all comments made by this user.
     *
     * Defines a one-to-many relationship between
     * User and Comment models.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'comment_user_id');
    }

    /**
     * Get the user who originally shared the post.
     *
     * Defines a belongs-to relationship to retrieve
     * the sharing user in a shared post scenario.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'share_user_id');
    }

    /**
     * Get the original post shared by the user.
     *
     * Defines a belongs-to relationship linking
     * a shared post back to its original Post.
     */
    public function originalPost()
    {
        return $this->belongsTo(Post::class, 'share_original_post_id');
    }

    /**
     * Get all chatrooms where this user is stored as user one.
     *
     * Defines a one-to-many relationship between
     * User and Chatroom models (as first participant).
     */
    public function chatroomsAsUserOne()
    {
        return $this->hasMany(Chatroom::class, 'cr_user_one_id');
    }

    /**
     * Get all chatrooms where this user is stored as user two.
     *
     * Defines a one-to-many relationship between
     * User and Chatroom models (as second participant).
     */
    public function chatroomsAsUserTwo()
    {
        return $this->hasMany(Chatroom::class, 'cr_user_two_id');
    }

    /**
     * Retrieve all chatrooms that this user is part of.
     *
     * Combines chatroomsAsUserOne and chatroomsAsUserTwo
     * to return all private message threads for this user.
     */
    public function allChatrooms()
    {
        return Chatroom::where('cr_user_one_id', $this->id)
                       ->orWhere('cr_user_two_id', $this->id);
    }
}
