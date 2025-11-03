<?php

namespace App\Http\Validations;

/*
|--------------------------------------------------------------------------
| Post Validation Messages
|--------------------------------------------------------------------------
| Custom validation messages for post-related operations.
| These messages provide user-friendly feedback during post creation
| and validation processes within the social feed system.
|--------------------------------------------------------------------------
*/

class PostValidationMessages
{
    /**
     * Messages for creating a new post.
     *
     * Used in: PostController@createPost
     */
    public static function create()
    {
        return [
            'post_content.required' => 'Please enter something before posting.',
            'post_content.string'   => 'The post content must contain valid text.',
            'post_content.max'      => 'Your post must not exceed 1000 characters.',
        ];
    }
}
