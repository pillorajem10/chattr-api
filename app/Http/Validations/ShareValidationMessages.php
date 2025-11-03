<?php

namespace App\Http\Validations;

/*
|--------------------------------------------------------------------------
| Share Validation Messages
|--------------------------------------------------------------------------
| Custom validation messages for share-related operations.
| These messages provide user-friendly feedback during post sharing
| and input validation within the social feed system.
|--------------------------------------------------------------------------
*/

class ShareValidationMessages
{
    /**
     * Messages for sharing a post.
     *
     * Used in: ShareController@sharePost
     */
    public static function share()
    {
        return [
            'share_caption.string' => 'The caption must contain valid text.',
            'share_caption.max'    => 'Your caption must not exceed 1000 characters.',
        ];
    }
}
