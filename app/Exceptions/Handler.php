<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

/**
 * ==========================================================
 * Class: Handler
 * ----------------------------------------------------------
 * Customizes how application exceptions are handled globally.
 *
 * Purpose:
 * - Intercept and format authentication-related exceptions
 *   before returning them as API responses.
 * - Maintain consistency using the ResponseHelper utility.
 *
 * Key Override:
 * - unauthenticated(): Converts default Laravel unauthenticated
 *   exceptions into a structured JSON response.
 * ==========================================================
 */
class Handler extends ExceptionHandler
{
    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * This method handles unauthenticated access attempts by returning
     * a standardized unauthorized response using ResponseHelper.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return ResponseHelper::sendUnauthorized(
            'Unauthorized access. Please log in.',
            null
        );
    }
}
