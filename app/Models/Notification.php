<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'tbl_notifications';
    protected $fillable = [
        'notification_user_id',
        'notification_post_id',
        'notification_type',
        'notification_message',
        'notification_read',
    ];

    public $timestamps = true;

    /**
     * The user who received this notification.
     *
     * Defines the inverse of the one-to-many relationship.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'notification_user_id');
    }

    /**
     * The post associated with this notification.
     *
     * Defines the inverse of the one-to-many relationship.
     */
    public function post()
    {
        return $this->belongsTo(Post::class, 'notification_post_id');
    }
}
