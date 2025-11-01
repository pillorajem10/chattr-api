<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'tbl_comments';

    protected $fillable = [
        'comment_post_id',
        'comment_user_id',
        'comment_content',
    ];

    /**
     * The post that this comment belongs to.
     *
     * Defines the inverse of the one-to-many relationship.
     */
    public function post()
    {
        return $this->belongsTo(Post::class, 'comment_post_id');
    }


    /**
     * The user who sent this comment.
     *
     * Defines the inverse of the one-to-many relationship.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'comment_user_id');
    }
}
