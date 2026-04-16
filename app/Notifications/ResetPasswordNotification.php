<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Reset your password'))
            ->view('emails.reset-password', [
                'url' => $url,
                'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
            ]);
    }
}
