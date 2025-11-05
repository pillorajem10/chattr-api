<?php

namespace App\Helpers;

/**
 * ==========================================================
 * Helper: ResponseHelper
 * ----------------------------------------------------------
 * Centralized utility class for building consistent JSON
 * responses throughout the application.
 *
 * Purpose:
 * - Standardize API response formats.
 * - Reduce repetition in controllers and services.
 * - Provide uniform success, error, unauthorized, and
 *   paginated response structures.
 *
 * Each response includes:
 * - author: identifies the application (Chattr)
 * - msg:    response message or description
 * - success: boolean indicating operation result
 * - data:    payload or structured content
 * ==========================================================
 */
class ResponseHelper
{
    /**
     * Builds a standard JSON response.
     *
     * @param  bool   $success     Indicates success or failure.
     * @param  mixed  $data        The response payload.
     * @param  string $msg         Message describing the result.
     * @param  int    $statusCode  HTTP status code.
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @param  mixed   $data
     * @param  string  $msg
     * @param  int     $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function sendSuccess($data = null, $msg = 'Request successful', $statusCode = 200)
    {
        return self::baseResponse(true, $data, $msg, $statusCode);
    }

    /**
     * Returns an error response.
     *
     * @param  string  $msg
     * @param  mixed   $data
     * @param  int     $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function sendError($msg = 'An error occurred', $data = null, $statusCode = 400)
    {
        return self::baseResponse(false, $data, $msg, $statusCode);
    }

    /**
     * Returns an unauthorized response.
     *
     * @param  string  $msg
     * @param  mixed   $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function sendUnauthorized($msg = 'Unauthorized access', $data = null)
    {
        return self::baseResponse(false, $data, $msg, 401);
    }

    /**
     * Returns a paginated formatted response.
     *
     * @param  array   $data          Paginated records.
     * @param  int     $pageIndex     Current page index.
     * @param  int     $pageSize      Number of records per page.
     * @param  int     $totalPages    Total available pages.
     * @param  int     $totalRecords  Total record count.
     * @param  string  $msg           Message description.
     * @param  int     $statusCode    HTTP status code.
     * @return \Illuminate\Http\JsonResponse
     */
    public static function sendPaginatedResponse(
        $data,
        $pageIndex,
        $pageSize,
        $totalPages,
        $totalRecords,
        $msg = 'Request successful',
        $statusCode = 200
    ) {
        $formattedData = [
            'pageIndex'    => $pageIndex,
            'pageSize'     => $pageSize,
            'totalPages'   => $totalPages,
            'totalRecords' => $totalRecords,
            'records'      => $data,
        ];

        return self::baseResponse(true, $formattedData, $msg, $statusCode);
    }
}
