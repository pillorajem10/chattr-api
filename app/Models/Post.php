<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'tbl_posts';

    protected $fillable = [
        'post_user_id',
        'post_content',
        'post_is_shared',
        'post_share_id',
    ];

    public $timestamps = true;

    /**
     * The user who owns this post.
     *
     * Defines the inverse of the one-to-many relationship.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'post_user_id');
    }

    /**
     * Get all reactions associated with this post.
     *
     * This defines the one-to-many relationship between
     * the post and its reactions.
     */
    public function reactions()
    {
        return $this->hasMany(Reaction::class, 'reaction_post_id');
    }

    /**
     * Get all comments associated with this post.
     *
     * This defines the one-to-many relationship between
     * the post and its comments.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'comment_post_id');
    }

    /**
     * Get all shares associated with this post.
     *
     * This defines the one-to-many relationship between
     * the post and its shares.
     */
    public function shares()
    {
        return $this->hasMany(Share::class, 'share_original_post_id');
    }

    /**
     * Get the share associated with this post if it is a shared post.
     *
     * Defines the inverse of the one-to-many relationship.
     */
    public function share()
    {
        return $this->belongsTo(Share::class, 'post_share_id');
    }
}
