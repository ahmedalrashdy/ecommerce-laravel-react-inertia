<?php

namespace App\Notifications\Auth;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class ForcedPasswordReset extends ResetPassword
{
    /**
     * Create a notification instance.
     */
    public function __construct(#[\SensitiveParameter] string $token)
    {
        parent::__construct($token);
    }

    /**
     * Get the reset password notification mail message for the given URL.
     */
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject(Lang::get('Reset Password Required'))
            ->line(Lang::get('Your account requires a password reset before you can sign in.'))
            ->action(Lang::get('Reset Password'), $url)
            ->line(Lang::get('This password reset link will expire in :count minutes.', [
                'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
            ]))
            ->line(Lang::get('If you have questions, please contact support.'));
    }
}
