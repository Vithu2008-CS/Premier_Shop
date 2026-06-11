<?php

namespace App\Services;

use Stripe\Event;
use Stripe\PaymentIntent;
use Stripe\StripeClient;
use Stripe\Webhook;

/**
 * Thin wrapper around the Stripe SDK so checkout/webhook code depends on an
 * injectable service (easy to fake in tests) rather than static Stripe calls.
 *
 * Card data never passes through here — the browser sends it straight to Stripe
 * via the Payment Element. This service only creates/retrieves PaymentIntents
 * (server-priced) and verifies webhook signatures.
 */
class StripeService
{
    /** True only when a real (non-placeholder) secret key is configured. */
    public function isConfigured(): bool
    {
        $secret = (string) config('services.stripe.secret');

        return $secret !== '' && ! str_contains($secret, 'placeholder');
    }

    public function currency(): string
    {
        return strtolower((string) config('services.stripe.currency', 'gbp'));
    }

    protected function client(): StripeClient
    {
        return new StripeClient((string) config('services.stripe.secret'));
    }

    /**
     * Create a PaymentIntent for an amount in the smallest currency unit (pence).
     * The price breakdown is passed as metadata so it can be read back as the
     * authoritative source of truth when the order is finalised.
     */
    public function createPaymentIntent(int $amountMinor, array $metadata = []): PaymentIntent
    {
        return $this->client()->paymentIntents->create([
            'amount'                    => $amountMinor,
            'currency'                  => $this->currency(),
            'automatic_payment_methods' => ['enabled' => true],
            'metadata'                  => $metadata,
        ]);
    }

    public function retrievePaymentIntent(string $id): PaymentIntent
    {
        return $this->client()->paymentIntents->retrieve($id);
    }

    /** Verify and decode a webhook payload using the signing secret. */
    public function constructWebhookEvent(string $payload, string $signature): Event
    {
        return Webhook::constructEvent($payload, $signature, (string) config('services.stripe.webhook_secret'));
    }
}
