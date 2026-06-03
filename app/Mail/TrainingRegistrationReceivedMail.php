<?php

namespace App\Mail;

use App\Models\InscriptionFormation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrainingRegistrationReceivedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public InscriptionFormation $registration
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle inscription formation - ' . ($this->registration->formation?->titre ?? 'Formation'),
            replyTo: [
                new \Illuminate\Mail\Mailables\Address('contact@opportunetmondiale.com', 'Opportunet Mondiale'),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.training-registration-received'
        );
    }
}
