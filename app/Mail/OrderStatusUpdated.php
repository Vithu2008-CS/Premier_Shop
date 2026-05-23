<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Notifies the customer when their order status changes.
 *
 * Sent by Admin\OrderController when an admin updates the order status
 * (processing, shipped, delivered, cancelled). The subject line includes
 * the new status so it's immediately visible in the customer's inbox.
 *
 * The order is passed with its current status already updated so the
 * email template reflects the new state.
 */
class OrderStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your order #'.$this->order->order_number.' status has been updated to '.$this->order->status,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.status_updated',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
