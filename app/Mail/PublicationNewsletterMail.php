<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PublicationNewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(public array $payload)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: (string) $this->payload['subject'],
            replyTo: [
                new \Illuminate\Mail\Mailables\Address('contact@opportunetmondiale.com', 'Opportunet Mondiale'),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.publication-newsletter',
        );
    }
}
