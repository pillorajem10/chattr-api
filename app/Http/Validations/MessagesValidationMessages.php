<?php

namespace App\Http\Validations;

/*
|--------------------------------------------------------------------------
| Message Validation Messages
|--------------------------------------------------------------------------
| Custom validation messages for message-related operations.
| These messages provide user-friendly feedback during message sending
| and validation processes within the messaging system.
|--------------------------------------------------------------------------
*/

class MessageValidationMessages
{
    /**
     * Messages for sending a new message.
     *
     * Used in: MessageController@sendMessage
     */
    public static function send()
    {
        return [
            'message_receiver_id.required' => 'Receiver ID is required.',
            'message_receiver_id.integer'  => 'Receiver ID must be a valid integer.',
            'message_receiver_id.exists'   => 'The specified receiver does not exist.',
            'message_content.required'     => 'Please enter a message before sending.',
            'message_content.string'       => 'The message must contain valid text.',
            'message_content.max'          => 'Your message must not exceed 2000 characters.',
        ];
    }
}
