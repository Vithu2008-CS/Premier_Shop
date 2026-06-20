<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Tests\TestCase;

/**
 * Covers StripeWebhookController: the endpoint is public + CSRF-exempt, so it must
 * reject anything without a valid Stripe signature, and only act on verified events.
 * A fake StripeService stands in for signature verification (no live keys needed).
 */
class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function order(string $intentId, string $status = 'pending'): Order
    {
        $role = Role::firstOrCreate(['name' => 'customer'], ['display_name' => 'Customer', 'is_staff' => false]);
        $user = User::factory()->create(['role_id' => $role->id]);

        return Order::create([
            'user_id' => $user->id, 'order_number' => 'PS-'.strtoupper($intentId),
            'status' => 'pending', 'subtotal' => 20, 'total' => 25,
            'shipping_address' => ['address_line' => 'x', 'city' => 'y', 'phone' => 'z'],
            'payment_status' => $status, 'payment_method' => 'Debit/Credit Card',
            'payment_intent_id' => $intentId,
        ]);
    }

    /** Bind a fake that returns the given event, or throws a signature error. */
    private function fakeWebhook(?Event $event, bool $throw = false): void
    {
        $fake = new class($event, $throw) extends StripeService
        {
            public function __construct(private ?Event $event, private bool $throw) {}

            public function constructWebhookEvent(string $payload, string $signature): Event
            {
                if ($this->throw) {
                    throw new SignatureVerificationException('Invalid signature');
                }

                return $this->event;
            }
        };

        $this->app->instance(StripeService::class, $fake);
    }

    private function postWebhook()
    {
        return $this->call('POST', route('stripe.webhook'), [], [], [], ['HTTP_STRIPE_SIGNATURE' => 'sig'], 'payload');
    }

    public function test_succeeded_event_marks_order_paid(): void
    {
        config()->set('services.stripe.webhook_secret', 'whsec_test');
        $order = $this->order('pi_wh_1', 'pending');

        $this->fakeWebhook(Event::constructFrom([
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => ['id' => 'pi_wh_1']],
        ]));

        $this->postWebhook()->assertOk();

        $this->assertSame('completed', $order->fresh()->payment_status);
    }

    public function test_invalid_signature_is_rejected(): void
    {
        config()->set('services.stripe.webhook_secret', 'whsec_test');
        $order = $this->order('pi_wh_2', 'pending');

        $this->fakeWebhook(null, throw: true);

        $this->postWebhook()->assertStatus(400);

        // Order untouched.
        $this->assertSame('pending', $order->fresh()->payment_status);
    }

    public function test_webhook_is_a_noop_without_a_signing_secret(): void
    {
        config()->set('services.stripe.webhook_secret', null);
        $order = $this->order('pi_wh_3', 'pending');

        $this->postWebhook()->assertOk();

        $this->assertSame('pending', $order->fresh()->payment_status);
    }
}
