<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Helpers\ResponseHelper;
use App\Helpers\TokenHelper;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Get conversation between the authenticated user and another user.
     * 
     * Supports optional filtering by unread messages only.
     */
    public function getConversation(Request $request, $receiverId)
    {
        // Decode token to get the authenticated user
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Pagination parameters
        $pageIndex = (int) $request->query('pageIndex', 1);
        $pageSize  = (int) $request->query('pageSize', 20);

        // Optional filter (e.g. ?filter=unread)
        $filter = $request->query('filter', 'all');

        // Base query: all messages between both users
        $query = Message::where(function ($q) use ($user, $receiverId) {
                $q->where('message_sender_id', $user->id)
                ->where('message_receiver_id', $receiverId);
            })
            ->orWhere(function ($q) use ($user, $receiverId) {
                $q->where('message_sender_id', $receiverId)
                ->where('message_receiver_id', $user->id);
            });

        // Apply unread filter (only messages received by the current user)
        if ($filter === 'unread') {
            $query->where('message_receiver_id', $user->id)
                ->where('message_read', false);
        }

        // Order by latest first
        $query->orderBy('created_at', 'desc');

        // Count total records
        $totalRecords = $query->count();
        $totalPages   = ceil($totalRecords / $pageSize);

        // Fetch paginated results
        $messages = $query
            ->skip(($pageIndex - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        // Return paginated response
        return ResponseHelper::sendPaginatedResponse(
            $messages,
            $pageIndex,
            $pageSize,
            $totalPages,
            $totalRecords
        );
    }

    /**
     * Send a new message to another user.
     * 
     * Validates input data and creates a new message record.
     * Broadcasts the event to the receiver in real time.
     */
    public function sendMessage(Request $request)
    {
        // Decode token to get the authenticated user
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'message_receiver_id' => 'required|integer|exists:tbl_users,id',
            'message_content'     => 'required|string|max:2000',
        ]);

        // Return first validation error if any
        if ($validator->fails()) {
            $firstError = collect($validator->errors()->all())->first();
            return ResponseHelper::sendError($firstError, null, 422);
        }

        // Create a new message record
        $message = Message::create([
            'message_sender_id'   => $user->id,
            'message_receiver_id' => $request->message_receiver_id,
            'message_content'     => $request->message_content,
        ]);

        // Broadcast to the receiver in real time
        broadcast(new MessageSent($message))->toOthers();

        // Return success response
        return ResponseHelper::sendSuccess($message, 'Message sent successfully.', 201);
    }

    /**
     * Mark all messages from a specific sender as read.
     * 
     * Updates message_read status for messages between users,
     * and broadcasts a "MessageRead" event to notify the sender.
     */
    public function markConversationAsRead(Request $request, $senderId)
    {
        // Decode token to get the authenticated user
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Validate senderId
        if (!$senderId) {
            return ResponseHelper::sendError('Sender ID is required.', null, 400);
        }

        // Update message read status
        Message::where('message_sender_id', $senderId)
            ->where('message_receiver_id', $user->id)
            ->where('message_read', false)
            ->update(['message_read' => true]);

        // Broadcast read event to the sender for real-time UI update
        broadcast(new MessageRead($senderId, $user->id))->toOthers();

        // Return success response
        return ResponseHelper::sendSuccess(null, 'Conversation marked as read.', 200);
    }
}
