<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $password;
    public function __construct($data, $password)
    {
        $this->data = $data;
        $this->password = $password;
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('snizinkavolshebna@gmail.com', 'OSKANS'),
            subject: 'Reset password',
        );
    }
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.users.reset',
        );
    }
    public function attachments(): array
    {
        return [];
    }
}
