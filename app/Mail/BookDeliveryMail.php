<?php
namespace App\Mail;
use App\Models\BookOrder;
use Illuminate\Bus\Queueable; use Illuminate\Mail\Mailable; use Illuminate\Mail\Mailables\Attachment; use Illuminate\Mail\Mailables\Content; use Illuminate\Mail\Mailables\Envelope; use Illuminate\Queue\SerializesModels;
class BookDeliveryMail extends Mailable { use Queueable,SerializesModels; public function __construct(public BookOrder $order){} public function envelope(): Envelope{return new Envelope(subject:'Votre livre : '.$this->order->book->title);} public function content(): Content{return new Content(view:'emails.book-delivery');} public function attachments(): array{return [Attachment::fromStorageDisk('local',$this->order->secured_document_path)->as($this->order->book->slug.'.pdf')->withMime('application/pdf')];} }
