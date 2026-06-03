<?php

namespace App\Mail;

use App\Models\CandidatureOffre;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminCompanyApplicationValidatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CandidatureOffre $application,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('offers.application.admin_company_validation_mail.subject', [
                'offer' => $this->application->opportunite->titre,
            ]),
            replyTo: [
                new \Illuminate\Mail\Mailables\Address('contact@opportunetmondiale.com', 'Opportunet Mondiale'),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-company-application-validated',
        );
    }
}
