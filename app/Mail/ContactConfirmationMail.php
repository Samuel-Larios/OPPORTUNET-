<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactConfirmationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Contact $contact
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: (string) __('home.forms.contact.mail.user_subject', [
                'subject' => $this->contact->subjectLabel(),
            ]),
            replyTo: [
                new \Illuminate\Mail\Mailables\Address('contact@opportunetmondiale.com', 'Opportunet Mondiale'),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-confirmation',
        );
    }
}
