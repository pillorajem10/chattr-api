<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    use HasFactory;

    protected $table = 'tbl_shares';

    protected $fillable = [
        'share_original_post_id',
        'share_user_id',
    ];

    public $timestamps = true;

    /**
     * Get the post instance that represents this share record.
     *
     * Defines the inverse of a one-to-many relationship between
     * a shared post and its corresponding Share record.
     *
     * Example:
     * A post with post_is_shared = true can reference this Share record
     * to find its sharing metadata.
     */
    public function post()
    {
        return $this->belongsTo(Post::class, 'share_post_id');
    }

    /**
     * Get the user who performed the share action.
     *
     * Defines a belongs-to relationship linking this share
     * to the user who shared the post.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'share_user_id');
    }

    /**
     * Get the original post that was shared.
     *
     * Defines a belongs-to relationship linking this Share record
     * to the original Post that was shared by the user.
     *
     * This allows access to the content and author of the original post.
     */
    public function originalPost()
    {
        return $this->belongsTo(Post::class, 'share_original_post_id');
    }
}
