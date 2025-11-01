<?php

namespace App\Helpers;

class ResponseHelper
{
    /**
     * Builds a standard JSON response.
     */
    private static function baseResponse($success, $data, $msg, $statusCode)
    {
        return response()->json([
            'author'  => 'Chattr',
            'msg'     => $msg,
            'success' => $success,
            'data'    => $data,
        ], $statusCode);
    }

    /**
     * Returns a success response.
     */
    public static function sendSuccess($data = null, $msg = 'Request successful', $statusCode = 200)
    {
        return self::baseResponse(true, $data, $msg, $statusCode);
    }

    /**
     * Returns an error response.
     */
    public static function sendError($msg = 'An error occurred', $data = null, $statusCode = 400)
    {
        return self::baseResponse(false, $data, $msg, $statusCode);
    }

    /**
     * Returns an unauthorized response.
     */
    public static function sendUnauthorized($msg = 'Unauthorized access', $data = null)
    {
        return self::baseResponse(false, $data, $msg, 401);
    }
}
