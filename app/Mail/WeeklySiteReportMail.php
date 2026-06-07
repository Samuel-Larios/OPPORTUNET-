<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklySiteReportMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $report
     */
    public function __construct(
        public array $report
    ) {
    }

    public function envelope(): Envelope
    {
        $start = $this->report['period_start']->format('d/m/Y');
        $end = $this->report['period_end']->format('d/m/Y');

        return new Envelope(
            subject: "Rapport hebdomadaire du site ({$start} - {$end})",
            replyTo: [
                new \Illuminate\Mail\Mailables\Address('contact@opportunetmondiale.com', 'Opportunet Mondiale'),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-site-report'
        );
    }
}
