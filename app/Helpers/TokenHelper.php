<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * ==========================================================
 * Helper: TokenHelper
 * ----------------------------------------------------------
 * Provides utility methods for decoding and verifying
 * Laravel Sanctum bearer tokens from Authorization headers.
 *
 * Purpose:
 * - Extract, hash, and validate Sanctum tokens.
 * - Retrieve the authenticated user model associated
 *   with a valid personal access token.
 *
 * Common Usage:
 * TokenHelper::decodeToken($request->header('Authorization'));
 * ==========================================================
 */
class TokenHelper
{
    /**
     * Attempt to decode and verify a Sanctum bearer token.
     *
     * This helper:
     * - Extracts the token from the "Authorization" header.
     * - Hashes it (since Sanctum stores tokens in hashed form).
     * - Retrieves the associated user model if valid.
     *
     * @param  string|null  $header  The full "Authorization" header value.
     * @return \Illuminate\Database\Eloquent\Model|null  The authenticated user, or null if invalid.
     */
    public static function decodeToken($header)
    {
        // Return null if there’s no Authorization header or it’s not a Bearer token
        if (! $header || ! str_starts_with($header, 'Bearer ')) {
            return null;
        }

        // Extract the raw token string from the header
        $token = substr($header, 7);

        // Sanctum stores tokens as SHA-256 hashes, so hash before lookup
        $hashedToken = hash('sha256', $token);

        // Attempt to find the matching personal access token record
        $accessToken = PersonalAccessToken::where('token', $hashedToken)->first();

        // Return the associated user (tokenable) if found, otherwise null
        return $accessToken ? $accessToken->tokenable : null;
    }
}
