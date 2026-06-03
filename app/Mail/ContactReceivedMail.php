<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReceivedMail extends Mailable
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
            subject: 'Nouveau contact - ' . $this->contact->subjectLabel() . ' - ' . $this->contact->fullName(),
            replyTo: [
                new \Illuminate\Mail\Mailables\Address($this->contact->email, $this->contact->fullName()),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-received',
        );
    }
}
