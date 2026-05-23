<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Order confirmation email sent to the customer immediately after checkout.
 *
 * Sent by CheckoutController::process() after the DB transaction commits.
 * A copy of the rendered HTML is also archived in the ContactMessage sent
 * folder so admin can review all outbound order receipts in the mail centre.
 *
 * The Order is passed with items.product and user already loaded to avoid
 * N+1 queries inside the email template.
 */
class OrderReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Premier Shop Order #'.$this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-receipt',
        );
    }
}
