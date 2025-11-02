<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\ResponseHelper;
use App\Helpers\TokenHelper;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get all users except the authenticated user
     * 
     * with pagination response helper
     * 
     * @param Request $request
     */
    public function getAllUsers(Request $request)
    {
        // Decode token to get the authenticated user
        $user = TokenHelper::decodeToken($request->header('Authorization'));

        // Query parameters
        $pageIndex = (int) $request->query('pageIndex', 1);
        $pageSize  = (int) $request->query('pageSize', 10);
        $search    = $request->query('search');

        // Build base query
        $usersQuery = User::where('id', '!=', $user->id)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('user_fname', 'like', "%{$search}%")
                    ->orWhere('user_lname', 'like', "%{$search}%")
                    ->orWhere('user_email', 'like', "%{$search}%");
                });
            });

        // Get total count *after* filters applied
        $totalRecords = $usersQuery->count();
        $totalPages   = ceil($totalRecords / $pageSize);

        // Fetch paginated records
        $users = $usersQuery
            ->skip(($pageIndex - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        // Return standardized response
        return ResponseHelper::sendPaginatedResponse(
            $users,
            $pageIndex,
            $pageSize,
            $totalPages,
            $totalRecords
        );
    }

    /**
     * Get user by ID
     * 
     * @param Request $request
     * @param int $id
     */
    public function getUserById(Request $request, $id)
    {
        // Fetch user by ID
        $user = User::find($id);

        // Check if user exists
        if (!$user) {
            return ResponseHelper::sendError('User not found', null, 404);
        }

        // Return response
        return ResponseHelper::sendSuccess($user);
    }
}
