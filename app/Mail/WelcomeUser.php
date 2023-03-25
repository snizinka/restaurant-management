<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeUser extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('snizinkavolshebna@gmail.com', 'OSKANS'),
            subject: 'OrderService Shipped',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.users.welcome-email',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
