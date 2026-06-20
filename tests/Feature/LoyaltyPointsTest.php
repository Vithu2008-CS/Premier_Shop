<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserItem;
use App\Services\StripeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\PaymentIntent;
use Tests\TestCase;

/**
 * Loyalty points must only be EARNED on a paid order.
 *
 * Regression guard for the points-fraud vector: bank-transfer orders are created
 * with payment_status='pending', so awarding at creation let a customer place
 * unpaid orders, accrue points, and redeem them before ever paying. Points are
 * now granted by Order::awardLoyaltyPoints() — at checkout for already-paid card
 * orders, and on payment confirmation (admin / webhook) for bank transfers.
 */
class LoyaltyPointsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin', 'display_name' => 'Administrator', 'is_staff' => true]);
        $customerRole = Role::create(['name' => 'customer', 'display_name' => 'Customer', 'is_staff' => false]);

        $this->admin = User::factory()->create(['role_id' => Role::where('name', 'admin')->value('id')]);
        $this->customer = User::factory()->create(['role_id' => $customerRole->id, 'loyalty_points' => 0]);

        // Loyalty on, 1 point per £1, no £100 booster involved (orders are £20).
        Setting::create(['other_settings' => ['loyalty_enabled' => true, 'points_per_pound' => 1]]);

        $this->product = Product::factory()->create([
            'category_id' => Category::factory()->create()->id,
            'is_active'   => true,
            'price'       => 20,
            'stock'       => 10,
        ]);
    }

    private function addToCart(int $qty = 1): void
    {
        UserItem::create([
            'user_id'    => $this->customer->id,
            'product_id' => $this->product->id,
            'quantity'   => $qty,
            'type'       => 'cart',
        ]);
    }

    /** Bind a fake StripeService returning a succeeded, owned, £25 intent. */
    private function fakeStripe(): void
    {
        $intent = PaymentIntent::constructFrom([
            'id'       => 'pi_loyalty_1',
            'status'   => 'succeeded',
            'currency' => 'gbp',
            'amount'   => 2500, // £20 subtotal + £5 shipping
            'metadata' => [
                'user_id'         => (string) $this->customer->id,
                'subtotal'        => '20', 'discount' => '0',
                'coupon_code'     => '', 'coupon_id' => '',
                'points_discount' => '0', 'points_used' => '0',
                'shipping'        => '5', 'distance' => '', 'total' => '25',
            ],
        ]);

        $fake = new class($intent) extends StripeService {
            public function __construct(private PaymentIntent $intent) {}
            public function isConfigured(): bool { return true; }
            public function currency(): string { return 'gbp'; }
            public function retrievePaymentIntent(string $id): PaymentIntent { return $this->intent; }
        };

        $this->app->instance(StripeService::class, $fake);
    }

    public function test_bank_transfer_order_earns_no_points_until_paid(): void
    {
        $this->addToCart();

        $this->actingAs($this->customer)->post(route('checkout.process'), [
            'address_line'   => '1 Test Street',
            'city'           => 'London',
            'phone'          => '07123456789',
            'payment_method' => 'Bank Transfer',
        ])->assertStatus(302);

        // Order exists but is unpaid → no points granted, no 'earned' ledger row.
        $this->assertDatabaseHas('orders', [
            'user_id'        => $this->customer->id,
            'payment_status' => 'pending',
        ]);
        $this->assertSame(0, $this->customer->fresh()->loyalty_points);
        $this->assertDatabaseMissing('reward_point_transactions', [
            'user_id' => $this->customer->id,
            'type'    => 'earned',
        ]);
    }

    public function test_admin_confirming_bank_transfer_payment_awards_points_once(): void
    {
        $order = Order::create([
            'user_id'          => $this->customer->id,
            'order_number'     => 'PS-LOYALTY-BT',
            'status'           => 'pending',
            'subtotal'         => 20,
            'discount_amount'  => 0,
            'points_discount'  => 0,
            'total'            => 25,
            'shipping_address' => ['address_line' => 'x', 'city' => 'y', 'phone' => 'z'],
            'payment_method'   => 'Bank Transfer',
            'payment_status'   => 'pending',
        ]);

        // Confirm payment (status kept 'pending' so no status-change email fires).
        $confirm = fn () => $this->actingAs($this->admin)->patch(
            route('admin.orders.updateStatus', $order),
            ['status' => 'pending', 'payment_status' => 'completed']
        );

        $confirm()->assertStatus(302);
        $this->assertSame(20, $this->customer->fresh()->loyalty_points);

        // Re-confirming must not double-award (idempotent).
        $confirm()->assertStatus(302);
        $this->assertSame(20, $this->customer->fresh()->loyalty_points);
        $this->assertSame(1, $order->rewardPointTransactions()->where('type', 'earned')->count());
    }

    public function test_card_order_earns_points_on_creation(): void
    {
        $this->addToCart();
        $this->fakeStripe();

        $this->actingAs($this->customer)->post(route('checkout.process'), [
            'address_line'      => '1 Test Street',
            'city'              => 'London',
            'phone'             => '07123456789',
            'payment_method'    => 'Debit/Credit Card',
            'payment_intent_id' => 'pi_loyalty_1',
        ])->assertStatus(302);

        // Card order is paid at creation → 20 points earned immediately.
        $this->assertSame(20, $this->customer->fresh()->loyalty_points);
        $this->assertDatabaseHas('reward_point_transactions', [
            'user_id' => $this->customer->id,
            'type'    => 'earned',
            'amount'  => 20,
        ]);
    }
}
