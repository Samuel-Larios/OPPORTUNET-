<?php

namespace App\Mail;

use App\Models\JobSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiredReminderMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public JobSubscription $subscription)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Votre accès aux offres premium a expiré');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.subscription-expired-reminder');
    }
}
