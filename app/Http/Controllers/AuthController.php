<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     *
     * Validates input, hashes the password,
     * creates a new user, and returns an auth token.
     */
    public function register(Request $request)
    {
        // Check if user with email already exists
        if (User::where('user_email', $request->user_email)->exists()) {
            return ResponseHelper::sendError('Email already in use.', null, 409);
        }

        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'user_fname' => 'required|string|max:50',
            'user_lname' => 'required|string|max:50',
            'user_email' => 'required|email|max:100',
            'user_password' => 'required|string|min:6',
            'user_bio' => 'nullable|string|max:500',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            // get the first array for the message
            $firstError = collect($validator->errors()->all())->first();

            // Return the first validation error
            return ResponseHelper::sendError($firstError, null, 422);
        };

        // Create new user
        $user = User::create([
            'user_fname' => $request->user_fname,
            'user_lname' => $request->user_lname,
            'user_bio' => $request->user_bio ?? '',
            'user_email' => $request->user_email,
            'user_password' => Hash::make($request->user_password),
        ]);

        // Generate token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return response
        return ResponseHelper::sendSuccess([
            'user' => $user,
            'token' => $token,
        ], 'Account created successfully.', 201);
    }

    /**
     * Handle user login.
     *
     * Verifies credentials, removes existing tokens (if any),
     * and issues a new token upon successful authentication.
     */
    public function login(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'user_email' => 'required|email|max:100',
            'user_password' => 'required|string|min:6',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            // get the first array for the message
            $firstError = collect($validator->errors()->all())->first();

            // Return the first validation error
            return ResponseHelper::sendError($firstError, null, 422);
        };

        // Retrieve user by email
        $user = User::where('user_email', $request->user_email)->first();

        // Validate user credentials
        if (!$user || !Hash::check($request->user_password, $user->user_password)) {
            return ResponseHelper::sendError('Invalid email or password.', null, 401);
        }

        // Revoke existing tokens
        $user->tokens()->delete();

        // Generate new token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return response
        return ResponseHelper::sendSuccess([
            'user' => $user,
            'token' => $token,
        ], 'Login successful.', 200);
    }

    /**
     * Handle user logout.
     *
     * Revokes the current access token for the authenticated user.
     */
    public function logout(Request $request)
    {
        // Revoke the current access token
        $request->user()->currentAccessToken()->delete();

        // Return response
        return ResponseHelper::sendSuccess(null, 'Logged out successfully.');
    }

    /**
     * Retrieve the authenticated user's details.
     *
     * Returns basic user information for the current session.
     */
    public function me(Request $request)
    {
        // Return response
        return ResponseHelper::sendSuccess($request->user());
    }
}
