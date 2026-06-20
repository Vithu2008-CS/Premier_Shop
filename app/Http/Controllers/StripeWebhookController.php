<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Receives Stripe webhooks. The endpoint is public and CSRF-exempt, so the
 * payload is authenticated solely by its Stripe signature header — an unsigned
 * or tampered request is rejected with 400.
 *
 * The order is normally created synchronously in CheckoutController::process()
 * after the client confirms payment; this webhook is a backstop that reconciles
 * payment status (e.g. if the customer closed the tab after paying).
 */
class StripeWebhookController extends Controller
{
    public function __construct(private StripeService $stripe) {}

    public function handle(Request $request)
    {
        // Nothing to verify against if no signing secret is configured.
        if (! config('services.stripe.webhook_secret')) {
            return response()->json(['ignored' => true]);
        }

        try {
            $event = $this->stripe->constructWebhookEvent(
                $request->getContent(),
                $request->header('Stripe-Signature', '')
            );
        } catch (\Throwable $e) {
            Log::warning('Stripe webhook signature verification failed: '.$e->getMessage());

            return response()->json(['error' => 'Invalid signature.'], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;
            $order = Order::where('payment_intent_id', $intent->id)->first();

            if ($order && $order->payment_status !== 'completed') {
                $order->update(['payment_status' => 'completed']);
                // Grant earned points now that payment is confirmed (idempotent —
                // card orders confirmed synchronously have already earned theirs).
                $order->awardLoyaltyPoints();
                Log::info("Stripe webhook marked order {$order->order_number} paid.");
            }
        }

        // Acknowledge so Stripe stops retrying.
        return response()->json(['received' => true]);
    }
}
