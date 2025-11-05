<?php

namespace App\Http\Controllers;

use App\Events\ChatroomCreated;
use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Helpers\ResponseHelper;
use App\Helpers\TokenHelper;
use App\Http\Validations\MessageValidationMessages;
use App\Models\Chatroom;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * ==========================================================
 * Controller: MessageController
 * ----------------------------------------------------------
 * Handles all message-related operations including:
 * - Retrieving chatrooms
 * - Fetching conversation messages
 * - Creating chatrooms
 * - Sending messages
 * - Marking messages as read
 *
 * Integrations:
 * - TokenHelper: Verifies authenticated user from bearer token.
 * - ResponseHelper: Provides standardized API responses.
 * - Broadcasting Events: Enables real-time communication.
 * ==========================================================
 */
class MessageController extends Controller
{
    /**
     * Get all chatrooms the authenticated user participates in.
     *
     * Each chatroom includes:
     * - The latest message preview
     * - Unread message count
     *
     * Supports optional filtering via query:
     * - ?filter=unread — Returns only chatrooms with unread messages.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserChatrooms(Request $request)
    {
        $user = TokenHelper::decodeToken($request->header('Authorization'));
        $filter = $request->query('filter', 'all');

        // Retrieve all chatrooms where the user is a participant
        $chatrooms = Chatroom::where(function ($q) use ($user) {
                $q->where('cr_user_one_id', $user->id)
                  ->orWhere('cr_user_two_id', $user->id);
            })
            ->with([
                'userOne:id,user_fname,user_lname',
                'userTwo:id,user_fname,user_lname',
                'messages' => function ($q) {
                    $q->latest()->limit(1);
                },
            ])
            ->get();

        // Append unread message count to each chatroom
        foreach ($chatrooms as $chatroom) {
            $chatroom->unread_count = Message::where('message_chatroom_id', $chatroom->id)
                ->where('message_receiver_id', $user->id)
                ->where('message_read', false)
                ->count();
        }

        // Apply unread filter if requested
        if ($filter === 'unread') {
            $chatrooms = $chatrooms->filter(fn($chatroom) => $chatroom->unread_count > 0)->values();
        }

        // Sort chatrooms by latest message timestamp
        $chatrooms = $chatrooms->sortByDesc(
            fn($chatroom) => optional($chatroom->messages->first())->created_at
        )->values();

        return ResponseHelper::sendSuccess($chatrooms, 'Chatrooms retrieved successfully.');
    }

    /**
     * Retrieve conversation messages within a specific chatroom.
     *
     * Supports:
     * - Pagination via pageIndex & pageSize
     * - Optional filtering (?filter=unread)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $chatroomId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConversation(Request $request, $chatroomId)
    {
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        $pageIndex = (int) $request->query('pageIndex', 1);
        $pageSize  = (int) $request->query('pageSize', 20);
        $filter    = $request->query('filter', 'all');

        $chatroom = Chatroom::find($chatroomId);

        // Ensure chatroom exists and user is a participant
        if (! $chatroom || ! $chatroom->hasParticipant($user->id)) {
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
     * Create a new private chatroom between two users.
     *
     * Prevents duplicate chatrooms and self-chat creation.
     * Fires real-time ChatroomCreated event upon success.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createChatroom(Request $request)
    {
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Validate receiver ID
        $validator = Validator::make(
            $request->all(),
            [
                'receiver_id' => 'required|integer|exists:tbl_users,id',
            ],
            [
                'receiver_id.required' => 'Receiver ID is required.',
                'receiver_id.exists'   => 'The specified user does not exist.',
            ]
        );

        if ($validator->fails()) {
            $firstError = collect($validator->errors()->all())->first();
            return ResponseHelper::sendError($firstError, null, 422);
        }

        // Prevent self-chat creation
        if ((int) $request->receiver_id === (int) $user->id) {
            return ResponseHelper::sendError('You cannot create a chatroom with yourself.', null, 422);
        }

        // Check if chatroom already exists
        $existing = Chatroom::where(function ($q) use ($user, $request) {
                $q->where('cr_user_one_id', $user->id)
                  ->where('cr_user_two_id', $request->receiver_id);
            })
            ->orWhere(function ($q) use ($user, $request) {
                $q->where('cr_user_one_id', $request->receiver_id)
                  ->where('cr_user_two_id', $user->id);
            })
            ->first();

        if ($existing) {
            return ResponseHelper::sendSuccess([
                'chatroom' => $existing,
                'new_chatroom' => false,
            ], 'Chatroom already exists.');
        }

        // Create new chatroom
        $chatroom = Chatroom::create([
            'cr_user_one_id' => $user->id,
            'cr_user_two_id' => $request->receiver_id,
        ]);

        // Broadcast new chatroom creation
        broadcast(new ChatroomCreated($chatroom))->toOthers();

        return ResponseHelper::sendSuccess([
            'chatroom' => $chatroom,
            'new_chatroom' => true,
        ], 'Chatroom created successfully.', 201);
    }

    /**
     * Send a new private message inside an existing chatroom.
     *
     * Validates input, ensures authorization, creates the message,
     * and broadcasts it in real time.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Validate message payload
        $validator = Validator::make(
            $request->all(),
            [
                'message_receiver_id' => 'required|integer|exists:tbl_users,id',
                'message_chatroom_id' => 'required|integer|exists:tbl_chatrooms,id',
                'message_content'     => 'required|string|max:2000',
            ],
            MessageValidationMessages::send()
        );

        if ($validator->fails()) {
            $firstError = collect($validator->errors()->all())->first();
            return ResponseHelper::sendError($firstError, null, 422);
        }

        // Ensure chatroom exists and user is a participant
        $chatroom = Chatroom::find($request->message_chatroom_id);
        if (! $chatroom || ! $chatroom->hasParticipant($user->id)) {
            return ResponseHelper::sendError('You are not authorized to send messages in this chatroom.', null, 403);
        }

        // Create the new message
        $message = Message::create([
            'message_sender_id'   => $user->id,
            'message_receiver_id' => $request->message_receiver_id,
            'message_chatroom_id' => $chatroom->id,
            'message_content'     => $request->message_content,
        ]);

        // Broadcast message to both participants
        broadcast(new MessageSent($message))->toOthers();

        return ResponseHelper::sendSuccess([
            'chatroom' => $chatroom,
            'message'  => $message,
        ], 'Message sent successfully.', 201);
    }

    /**
     * Mark all messages in a chatroom as read for the authenticated user.
     *
     * Updates message read status and triggers real-time broadcast.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $chatroomId
     * @return \Illuminate\Http\JsonResponse
     */
    public function markConversationAsRead(Request $request, $chatroomId)
    {
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        $chatroom = Chatroom::find($chatroomId);
        if (! $chatroom || ! $chatroom->hasParticipant($user->id)) {
            return ResponseHelper::sendError('You are not authorized to access this chatroom.', null, 403);
        }

        // Mark unread messages as read
        Message::where('message_chatroom_id', $chatroomId)
            ->where('message_receiver_id', $user->id)
            ->where('message_read', false)
            ->update(['message_read' => true]);

        $senderId = ($chatroom->cr_user_one_id === $user->id)
            ? $chatroom->cr_user_two_id
            : $chatroom->cr_user_one_id;

        // Broadcast message read event to both participants
        broadcast(new MessageRead($chatroom->id, $senderId, $user->id))->toOthers();

        return ResponseHelper::sendSuccess(null, 'Messages marked as read.', 200);
    }
}
