<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * One-time password email sent during registration for email verification.
 *
 * The OTP is a short numeric code generated in AuthController::register()
 * and stored temporarily in the session. This mailable passes both the
 * code and the user's name to the view for a personalised message.
 */
class RegistrationOtp extends Mailable
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
            subject: 'Your Premier Shop Verification Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-otp',
        );
    }
}
