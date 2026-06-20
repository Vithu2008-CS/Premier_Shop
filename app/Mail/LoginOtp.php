<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * One-time sign-in code emailed as the second authentication factor.
 *
 * Issued by MfaService::sendCode() after a correct password when the account
 * requires MFA (all staff/drivers, plus customers who opted in). Carries the
 * plaintext code and the user's name for a personalised message; only the
 * hash of the code is ever stored server-side.
 */
class LoginOtp extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;

    public string $userName;

    public function __construct(string $otp, string $userName)
    {
        $this->otp = $otp;
        $this->userName = $userName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Premier Shop Sign-In Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.login-otp',
        );
    }
}
