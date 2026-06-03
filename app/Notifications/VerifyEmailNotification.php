<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends BaseVerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('admin.auth.verify_mail.subject'))
            ->replyTo('contact@opportunetmondiale.com', 'Opportunet Mondiale')
            ->view('emails.verify-email', [
                'user' => $notifiable,
                'verificationUrl' => $this->verificationUrl($notifiable),
            ]);
    }
}
