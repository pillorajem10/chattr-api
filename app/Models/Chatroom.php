<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chatroom extends Model
{
    use HasFactory;

    protected $table = 'tbl_chatrooms';

    protected $fillable = [
        'cr_user_one_id',
        'cr_user_two_id',
    ];

    /**
     * Get all messages that belong to this chatroom.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'message_chatroom_id');
    }

    /**
     * First participant in the chatroom.
     */
    public function userOne()
    {
        return $this->belongsTo(User::class, 'cr_user_one_id');
    }

    /**
     * Second participant in the chatroom.
     */
    public function userTwo()
    {
        return $this->belongsTo(User::class, 'cr_user_two_id');
    }

    /**
     * Helper method to check if a given user is part of this chatroom.
     */
    public function hasParticipant($userId): bool
    {
        return $this->cr_user_one_id === $userId || $this->cr_user_two_id === $userId;
    }

    /**
     * Static helper to find or create a chatroom between two users.
     */
    public static function getOrCreate($userA, $userB)
    {
        // Normalize user order so 1-2 and 2-1 don't create duplicates
        $chatroom = self::where(function ($q) use ($userA, $userB) {
            $q->where('cr_user_one_id', $userA)
              ->where('cr_user_two_id', $userB);
        })->orWhere(function ($q) use ($userA, $userB) {
            $q->where('cr_user_one_id', $userB)
              ->where('cr_user_two_id', $userA);
        })->first();

        // If not existing, create it
        if (!$chatroom) {
            $chatroom = self::create([
                'cr_user_one_id' => $userA,
                'cr_user_two_id' => $userB,
            ]);
        }

        return $chatroom;
    }
}
