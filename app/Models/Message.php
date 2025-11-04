<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'tbl_messages';

    protected $fillable = [
        'message_sender_id',
        'message_receiver_id',
        'message_content',
        'message_read',
        'message_chatroom_id',
    ];

    public $timestamps = true;

    /**
     * The user who sent this message.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'message_sender_id');
    }

    /**
     * The user who received this message.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'message_receiver_id');
    }

    /**
     * The chatroom this message belongs to.
     */
    public function chatroom()
    {
        return $this->belongsTo(Chatroom::class, 'message_chatroom_id');
    }
}
