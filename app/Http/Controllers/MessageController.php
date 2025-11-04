<?php

namespace App\Http\Controllers;

use App\Events\ChatroomCreated;
use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Helpers\ResponseHelper;
use App\Helpers\TokenHelper;
use App\Http\Validations\MessageValidationMessages;
use App\Http\Validations\CommentValidationMessages;
use App\Models\Chatroom;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Get all chatrooms the authenticated user participates in.
     *
     * Returns each chatroom with the latest message preview.
     */
    public function getUserChatrooms(Request $request)
    {
        $user = TokenHelper::decodeToken($request->header('Authorization'));
        $filter = $request->query('filter', 'all'); 

        // Get all chatrooms the user is part of
        $chatrooms = Chatroom::where(function ($q) use ($user) {
                $q->where('cr_user_one_id', $user->id)
                ->orWhere('cr_user_two_id', $user->id);
            })
            ->with([
                'userOne:id,user_fname,user_lname',
                'userTwo:id,user_fname,user_lname',
                'messages' => function ($q) {
                    $q->latest()->limit(1); // get latest message only
                }
            ])
            ->get();

        // Attach unread count for each chatroom
        foreach ($chatrooms as $chatroom) {
            $chatroom->unread_count = Message::where('message_chatroom_id', $chatroom->id)
                ->where('message_receiver_id', $user->id)
                ->where('message_read', false)
                ->count();
        }

        if ($filter === 'unread') {
            $chatrooms = $chatrooms->filter(function ($chatroom) {
                return $chatroom->unread_count > 0;
            })->values(); 
        }

        // Sort chatrooms by latest message timestamp
        $chatrooms = $chatrooms->sortByDesc(function ($chatroom) {
            return optional($chatroom->messages->first())->created_at;
        })->values();

        return ResponseHelper::sendSuccess($chatrooms, 'Chatrooms retrieved successfully.');
    }

    /**
     * Get conversation messages within a specific chatroom.
     */
    public function getConversation(Request $request, $chatroomId)
    {
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        $pageIndex = (int) $request->query('pageIndex', 1);
        $pageSize  = (int) $request->query('pageSize', 20);
        $filter    = $request->query('filter', 'all');

        $chatroom = Chatroom::find($chatroomId);

        if (!$chatroom || !$chatroom->hasParticipant($user->id)) {
            return ResponseHelper::sendError('You are not authorized to view this chatroom.', null, 403);
        }

        $query = Message::where('message_chatroom_id', $chatroomId);

        if ($filter === 'unread') {
            $query->where('message_receiver_id', $user->id)
                  ->where('message_read', false);
        }

        $totalRecords = $query->count();
        $totalPages   = ceil($totalRecords / $pageSize);

        $messages = $query->orderBy('created_at', 'desc')
            ->skip(($pageIndex - 1) * $pageSize)
            ->take($pageSize)
            ->with(['sender:id,user_fname,user_lname'])
            ->get();

        return ResponseHelper::sendPaginatedResponse(
            $messages,
            $pageIndex,
            $pageSize,
            $totalPages,
            $totalRecords
        );
    }

    /**
     * Send a new private message.
     * Automatically creates or retrieves a chatroom between users.
     */
    public function sendMessage(Request $request)
    {
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        $validator = Validator::make($request->all(), [
            'message_receiver_id' => 'required|integer|exists:tbl_users,id',
            'message_content'     => 'required|string|max:2000',
        ], MessageValidationMessages::send());

        if ($validator->fails()) {
            $firstError = collect($validator->errors()->all())->first();
            return ResponseHelper::sendError($firstError, null, 422);
        }

        // Find or create chatroom
        $chatroom = Chatroom::where(function ($q) use ($user, $request) {
                $q->where('cr_user_one_id', $user->id)
                  ->where('cr_user_two_id', $request->message_receiver_id);
            })
            ->orWhere(function ($q) use ($user, $request) {
                $q->where('cr_user_one_id', $request->message_receiver_id)
                  ->where('cr_user_two_id', $user->id);
            })
            ->first();

        $isNewChatroom = false;

        if (!$chatroom) {
            $chatroom = Chatroom::create([
                'cr_user_one_id' => $user->id,
                'cr_user_two_id' => $request->message_receiver_id,
            ]);

            $isNewChatroom = true;

            // Broadcast event for both users when a new chatroom is created
            broadcast(new ChatroomCreated($chatroom))->toOthers();
        }

        // Create message inside that chatroom
        $message = Message::create([
            'message_sender_id'   => $user->id,
            'message_receiver_id' => $request->message_receiver_id,
            'message_chatroom_id' => $chatroom->id,
            'message_content'     => $request->message_content,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        $responseData = [
            'chatroom' => $chatroom,
            'message'  => $message,
            'new_chatroom' => $isNewChatroom,
        ];

        return ResponseHelper::sendSuccess($responseData, 'Message sent successfully.', 201);
    }

    /**
     * Mark all messages in a chatroom as read for the authenticated user.
     */
    public function markConversationAsRead(Request $request, $chatroomId)
    {
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        $chatroom = Chatroom::find($chatroomId);
        if (!$chatroom || !$chatroom->hasParticipant($user->id)) {
            return ResponseHelper::sendError('You are not authorized to access this chatroom.', null, 403);
        }

        Message::where('message_chatroom_id', $chatroomId)
            ->where('message_receiver_id', $user->id)
            ->where('message_read', false)
            ->update(['message_read' => true]);

        // Now broadcast to the sender that their messages were read
        $senderId = ($chatroom->cr_user_one_id === $user->id)
            ? $chatroom->cr_user_two_id
            : $chatroom->cr_user_one_id;

        broadcast(new MessageRead($senderId, $user->id))->toOthers();

        return ResponseHelper::sendSuccess(null, 'Messages marked as read.', 200);
    }
}
