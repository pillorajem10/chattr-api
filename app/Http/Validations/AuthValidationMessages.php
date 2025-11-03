<?php

namespace App\Http\Validations;

/*
|--------------------------------------------------------------------------
| Auth Validation Messages
|--------------------------------------------------------------------------
| Custom validation messages for authentication-related operations.
| These messages provide user-friendly feedback during registration and login.
|--------------------------------------------------------------------------
*/

class AuthValidationMessages
{
    public static function register()
    {
        return [
            'user_fname.required' => 'Please enter your first name.',
            'user_fname.max' => 'First name must not exceed 50 characters.',
            'user_lname.required' => 'Please enter your last name.',
            'user_lname.max' => 'Last name must not exceed 50 characters.',
            'user_email.required' => 'Please provide your email address.',
            'user_email.email' => 'Please enter a valid email format.',
            'user_email.max' => 'Email must not exceed 100 characters.',
            'user_password.required' => 'You need to set a password.',
            'user_password.min' => 'Password must be at least 6 characters long.',
            'user_bio.max' => 'Bio must not exceed 500 characters.',
        ];
    }

    public static function login()
    {
        return [
            'user_email.required' => 'Please provide your email address.',
            'user_email.email' => 'Please enter a valid email format.',
            'user_password.required' => 'Please enter your password.',
            'user_password.min' => 'Password must be at least 6 characters long.',
        ];
    }
}
