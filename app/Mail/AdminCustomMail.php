<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable for custom emails composed by admin via the mail centre.
 *
 * Both subject and body are passed at construction time from the compose form.
 * The body (mailMessage) is rendered as HTML in emails.admin_custom — the
 * view applies Str::markdown() so admin can write plain text or markdown.
 *
 * Used by Admin\MailController::send() when dispatching to one or more recipients.
 */
class AdminCustomMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailSubject;

    public $mailMessage;

    public function __construct($subject, $message)
    {
        $this->mailSubject = $subject;
        $this->mailMessage = $message;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_custom',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
