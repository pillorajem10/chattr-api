<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class TokenHelper
{
    /**
     * Attempt to decode and verify a Sanctum bearer token.
     *
     * This helper extracts the token from the Authorization header,
     * hashes it (since Sanctum stores hashed tokens), and retrieves
     * the associated user model if the token exists and is valid.
     *
     * @param  string|null                              $header The full "Authorization" header value.
     * @return \Illuminate\Database\Eloquent\Model|null The authenticated user or null if invalid.
     */
    public static function decodeToken($header)
    {
        // Return null if no Authorization header or it’s not a Bearer token
        if (! $header || ! str_starts_with($header, 'Bearer ')) {
            return null;
        }

        // Extract the raw token string from the header
        $token = substr($header, 7);

        // Sanctum stores tokens as SHA-256 hashes, so hash before lookup
        $hashedToken = hash('sha256', $token);

        // Find the matching personal access token record
        $accessToken = PersonalAccessToken::where('token', $hashedToken)->first();

        // Return the user (tokenable) if found, otherwise null
        return $accessToken ? $accessToken->tokenable : null;
    }
}
