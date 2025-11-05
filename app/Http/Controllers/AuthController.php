<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Validations\AuthValidationMessages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * ==========================================================
 * Controller: AuthController
 * ----------------------------------------------------------
 * Handles user authentication processes, including:
 * - Registration
 * - Login
 * - Logout
 * - Retrieving authenticated user details
 *
 * Utilizes:
 * - ResponseHelper for standardized API responses
 * - Laravel Sanctum for token-based authentication
 * ==========================================================
 */
class AuthController extends Controller
{
    /**
     * Handle user registration.
     *
     * Validates input data, checks for duplicate emails,
     * hashes the password, creates a new user,
     * and issues a personal access token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Check if user with the provided email already exists
        if (User::where('user_email', $request->user_email)->exists()) {
            return ResponseHelper::sendError('Email already in use.', null, 409);
        }

        // Validate request data
        $validator = Validator::make(
            $request->all(),
            [
                'user_fname'    => 'required|string|max:50',
                'user_lname'    => 'required|string|max:50',
                'user_email'    => 'required|email|max:100',
                'user_password' => 'required|string|min:6',
                'user_bio'      => 'nullable|string|max:500',
            ],
            AuthValidationMessages::register()
        );

        // Return first validation error if validation fails
        if ($validator->fails()) {
            $firstError = collect($validator->errors()->all())->first();
            return ResponseHelper::sendError($firstError, null, 422);
        }

        // Create new user record
        $user = User::create([
            'user_fname'    => $request->user_fname,
            'user_lname'    => $request->user_lname,
            'user_bio'      => $request->user_bio ?? '',
            'user_email'    => $request->user_email,
            'user_password' => Hash::make($request->user_password),
        ]);

        // Generate Sanctum token for the new user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return success response with user and token
        return ResponseHelper::sendSuccess([
            'user'  => $user,
            'token' => $token,
        ], 'Account created successfully.', 201);
    }

    /**
     * Handle user login.
     *
     * Validates credentials, checks password authenticity,
     * revokes existing tokens, and issues a new token upon success.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate request data
        $validator = Validator::make(
            $request->all(),
            [
                'user_email'    => 'required|email|max:100',
                'user_password' => 'required|string|min:6',
            ],
            AuthValidationMessages::login()
        );

        // Return first validation error if validation fails
        if ($validator->fails()) {
            $firstError = collect($validator->errors()->all())->first();
            return ResponseHelper::sendError($firstError, null, 422);
        }

        // Retrieve user by email
        $user = User::where('user_email', $request->user_email)->first();

        // Verify credentials
        if (! $user || ! Hash::check($request->user_password, $user->user_password)) {
            return ResponseHelper::sendError('Invalid email or password.', null, 401);
        }

        // Revoke existing tokens to ensure only one session is active
        $user->tokens()->delete();

        // Generate new token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return success response with user and token
        return ResponseHelper::sendSuccess([
            'user'  => $user,
            'token' => $token,
        ], 'Login successful.', 200);
    }

    /**
     * Handle user logout.
     *
     * Revokes the current access token for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Revoke current access token
        $request->user()->currentAccessToken()->delete();

        // Return logout success response
        return ResponseHelper::sendSuccess(null, 'Logged out successfully.');
    }

    /**
     * Retrieve the authenticated user's details.
     *
     * Returns the currently authenticated user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return ResponseHelper::sendSuccess($request->user());
    }
}
