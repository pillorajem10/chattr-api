<?php

namespace App\Http\Validations;

/*
|--------------------------------------------------------------------------
| Comment Validation Messages
|--------------------------------------------------------------------------
| Custom validation messages for comment-related operations.
| These messages provide user-friendly feedback during comment creation.
|--------------------------------------------------------------------------
*/

class CommentValidationMessages
{
    /**
     * Messages for creating a new comment.
     *
     * Used in: CommentController@commentOnPost
     */
    public static function create()
    {
        return [
            'comment_content.required' => 'Please enter a comment before submitting.',
            'comment_content.string'   => 'The comment must contain valid text.',
            'comment_content.max'      => 'Your comment must not exceed 500 characters.',
        ];
    }
}
