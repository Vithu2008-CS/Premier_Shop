<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Models\UserItem;
use App\Services\StripeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\PaymentIntent;
use Tests\TestCase;

/**
 * Verifies the server-side Stripe payment checks in CheckoutController::process().
 * A fake StripeService stands in for the SDK, so the security-critical verification
 * (intent must be succeeded, ours, right currency, and unused) is proven without
 * any live Stripe keys or network calls.
 */
class CheckoutPaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'customer', 'display_name' => 'Customer', 'is_staff' => false]);
        $this->user = User::factory()->create(['role_id' => $role->id]);

        $this->product = Product::factory()->create([
            'name' => 'PayWidget',
            'category_id' => Category::factory()->create()->id,
            'is_active' => true,
            'price' => 20,
            'stock' => 10,
        ]);
    }

    private function addToCart(int $qty = 1): void
    {
        UserItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => $qty,
            'type' => 'cart',
        ]);
    }

    /** Bind a fake StripeService that reports configured and returns the given intent. */
    private function fakeStripe(?PaymentIntent $intent, bool $configured = true): void
    {
        $fake = new class($intent, $configured) extends StripeService
        {
            public function __construct(private ?PaymentIntent $intent, private bool $configured) {}

            public function isConfigured(): bool
            {
                return $this->configured;
            }

            public function currency(): string
            {
                return 'gbp';
            }

            public function retrievePaymentIntent(string $id): PaymentIntent
            {
                return $this->intent;
            }
        };

        $this->app->instance(StripeService::class, $fake);
    }

    private function intent(array $overrides = []): PaymentIntent
    {
        return PaymentIntent::constructFrom(array_merge([
            'id' => 'pi_test_123',
            'status' => 'succeeded',
            'currency' => 'gbp',
            'amount' => 2500, // £25.00 (subtotal £20 + £5 shipping)
            'metadata' => [
                'user_id' => (string) $this->user->id,
                'subtotal' => '20', 'discount' => '0',
                'coupon_code' => '', 'coupon_id' => '',
                'points_discount' => '0', 'points_used' => '0',
                'shipping' => '5', 'distance' => '', 'total' => '25',
            ],
        ], $overrides));
    }

    private function placeCardOrder(string $intentId = 'pi_test_123')
    {
        return $this->actingAs($this->user)->post(route('checkout.process'), [
            'address_line' => '1 Test Street',
            'city' => 'London',
            'phone' => '07123456789',
            'payment_method' => 'Debit/Credit Card',
            'payment_intent_id' => $intentId,
        ]);
    }

    public function test_card_order_created_when_payment_intent_succeeds(): void
    {
        $this->addToCart();
        $this->fakeStripe($this->intent());

        $this->placeCardOrder()->assertStatus(302);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'payment_status' => 'completed',
            'payment_intent_id' => 'pi_test_123',
        ]);
        // Stock decremented inside the transaction.
        $this->assertSame(9, $this->product->fresh()->stock);
    }

    public function test_card_order_rejected_when_intent_not_succeeded(): void
    {
        $this->addToCart();
        $this->fakeStripe($this->intent(['status' => 'requires_payment_method']));

        $this->placeCardOrder()->assertSessionHas('error');

        $this->assertDatabaseMissing('orders', ['user_id' => $this->user->id]);
        $this->assertSame(10, $this->product->fresh()->stock);
    }

    public function test_card_order_rejected_when_intent_belongs_to_another_user(): void
    {
        $this->addToCart();
        $this->fakeStripe($this->intent(['metadata' => [
            'user_id' => '999999', 'subtotal' => '20', 'total' => '25', 'shipping' => '5',
        ]]));

        $this->placeCardOrder()->assertSessionHas('error');
        $this->assertDatabaseMissing('orders', ['user_id' => $this->user->id]);
    }

    public function test_card_order_rejected_on_wrong_currency(): void
    {
        $this->addToCart();
        $this->fakeStripe($this->intent(['currency' => 'usd']));

        $this->placeCardOrder()->assertSessionHas('error');
        $this->assertDatabaseMissing('orders', ['user_id' => $this->user->id]);
    }

    public function test_payment_intent_cannot_be_reused(): void
    {
        $this->addToCart();
        // An order already consumed this intent.
        Order::create([
            'user_id' => $this->user->id, 'order_number' => 'PS-EXISTING',
            'status' => 'pending', 'subtotal' => 20, 'total' => 25,
            'shipping_address' => ['address_line' => 'x', 'city' => 'y', 'phone' => 'z'],
            'payment_status' => 'completed', 'payment_method' => 'Debit/Credit Card',
            'payment_intent_id' => 'pi_test_123',
        ]);

        $this->fakeStripe($this->intent());

        $this->placeCardOrder()->assertSessionHas('error');

        // Still only the original order — no duplicate created.
        $this->assertSame(1, Order::where('user_id', $this->user->id)->count());
    }

    public function test_card_rejected_when_stripe_not_configured(): void
    {
        $this->addToCart();
        $this->fakeStripe(null, configured: false);

        $this->placeCardOrder()->assertSessionHas('error');
        $this->assertDatabaseMissing('orders', ['user_id' => $this->user->id]);
    }

    public function test_card_order_only_fulfils_pinned_items(): void
    {
        // Item A is priced + pinned into the intent; item B is added "after" pricing
        // and must NOT end up in the paid order.
        $itemA = UserItem::create([
            'user_id' => $this->user->id, 'product_id' => $this->product->id,
            'quantity' => 1, 'type' => 'cart',
        ]);
        $productB = Product::factory()->create([
            'name' => 'SmuggledWidget', 'category_id' => Category::factory()->create()->id,
            'is_active' => true, 'price' => 100, 'stock' => 5,
        ]);
        UserItem::create([
            'user_id' => $this->user->id, 'product_id' => $productB->id,
            'quantity' => 1, 'type' => 'cart',
        ]);

        $this->fakeStripe($this->intent(['metadata' => [
            'user_id' => (string) $this->user->id,
            'subtotal' => '20', 'discount' => '0', 'coupon_code' => '', 'coupon_id' => '',
            'points_discount' => '0', 'points_used' => '0', 'shipping' => '5', 'distance' => '',
            'total' => '25', 'item_ids' => (string) $itemA->id,
        ]]));

        $this->placeCardOrder()->assertStatus(302);

        $order = Order::where('user_id', $this->user->id)->latest('id')->first();
        $this->assertNotNull($order);
        $this->assertSame(1, $order->items()->count());
        $this->assertSame($this->product->id, $order->items()->first()->product_id);
        // Smuggled item never charged → stock intact.
        $this->assertSame(5, $productB->fresh()->stock);
    }

    public function test_bank_transfer_order_is_pending_and_needs_no_stripe(): void
    {
        $this->addToCart();
        $this->fakeStripe(null, configured: false);

        $this->actingAs($this->user)->post(route('checkout.process'), [
            'address_line' => '1 Test Street',
            'city' => 'London',
            'phone' => '07123456789',
            'payment_method' => 'Bank Transfer',
        ])->assertStatus(302);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'payment_method' => 'Bank Transfer',
            'payment_status' => 'pending',
            'payment_intent_id' => null,
        ]);
    }
}
