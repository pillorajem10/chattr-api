<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    use HasFactory;

    protected $table = 'tbl_reactions';

    protected $fillable = [
        'reaction_post_id',
        'reaction_user_id',
        'reaction_type',
    ];

    public $timestamps = true;

    /**
     * The post that this reaction belongs to.
     *
     * Defines the inverse of the one-to-many relationship.
     */
    public function post()
    {
        return $this->belongsTo(Post::class, 'reaction_post_id');
    }

    /**
     * The user who made this reaction.
     *
     * Defines the inverse of the one-to-many relationship.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'reaction_user_id');
    }
}
