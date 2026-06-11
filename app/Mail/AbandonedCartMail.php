<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

/**
 * Reminder email for a customer who left items in their cart without checking out.
 * Sent by the cart:remind-abandoned command. Lists the cart items and links back
 * to the cart so the customer can complete their order.
 */
class AbandonedCartMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Collection $items;
    public float $subtotal;

    public function __construct(User $user, Collection $items)
    {
        $this->user = $user;
        $this->items = $items;
        $this->subtotal = (float) $items->sum('line_total');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You left something behind 🛒',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.abandoned-cart',
        );
    }
}
