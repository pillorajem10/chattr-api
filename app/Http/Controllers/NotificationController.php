<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Helpers\TokenHelper;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user (with pagination)
     *
     * Filters: all, unread
     * @param Request $request
     *
     */
    public function getAllNotifications(Request $request)
    {
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        $pageIndex = (int) $request->query('pageIndex', 1);
        $pageSize  = (int) $request->query('pageSize', 10);
        $filter    = $request->query('filter', 'all');

        $notificationsQuery = Notification::where('notification_user_id', $user->id)
            ->when($filter === 'unread', function ($query) {
                $query->where('notification_read', false);
            });

        $totalRecords = $notificationsQuery->count();
        $totalPages   = ceil($totalRecords / $pageSize);

        $notifications = $notificationsQuery
            ->orderBy('created_at', 'desc')
            ->skip(($pageIndex - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        return ResponseHelper::sendPaginatedResponse(
            $notifications,
            $pageIndex,
            $pageSize,
            $totalPages,
            $totalRecords
        );
    }

    /**
     * Mark a single notification as read
     *
     * Real-time update via WebSocket
     */
    public function markAsRead(Request $request, $notificationId)
    {
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        $notification = Notification::where('id', $notificationId)
            ->where('notification_user_id', $user->id)
            ->first();

        if (! $notification) {
            return ResponseHelper::sendError('Notification not found', null, 404);
        }

        // If already read, just return success
        if ($notification->notification_read) {
            return ResponseHelper::sendSuccess($notification, 'Notification already marked as read.');
        }

        $notification->update(['notification_read' => true]);

        return ResponseHelper::sendSuccess($notification, 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read for the authenticated user
     *
     * Real-time updates via WebSocket
     */
    public function markAllAsRead(Request $request)
    {
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        $unread = Notification::where('notification_user_id', $user->id)
            ->where('notification_read', false)
            ->get();

        if ($unread->isEmpty()) {
            return ResponseHelper::sendSuccess(null, 'All notifications are already read.');
        }

        // Update all unread notifications in one query
        Notification::where('notification_user_id', $user->id)
            ->where('notification_read', false)
            ->update(['notification_read' => true]);

        return ResponseHelper::sendSuccess(null, 'All notifications marked as read.');
    }
}
