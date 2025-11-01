<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    use HasFactory;

    protected $table = 'tbl_shares';

    protected $fillable = [
        'share_post_id',
        'share_user_id',
        'share_caption',
    ];

    public $timestamps = true;

    /**
     * Get the post that was shared.
     *
     * Defines the inverse of the one-to-many relationship.
     */
    public function post()
    {
        return $this->belongsTo(Post::class, 'share_post_id');
    }

    /**
     * Get the user who shared the post.
     *
     * Defines the inverse of the one-to-many relationship.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'share_user_id');
    }
}
