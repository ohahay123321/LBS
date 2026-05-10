<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $verifyLink;

    public function __construct($verifyLink)
    {
        $this->verifyLink = $verifyLink;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Library Management System 2030 - Verify Your Email',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verify',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
