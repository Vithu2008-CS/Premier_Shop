<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\UserItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create([
            'stock' => 100,
            'price' => 29.99,
            'is_active' => true,
        ]);
    }

    /**
     * Test user can view checkout page with items in cart
     */
    public function test_user_can_view_checkout_page(): void
    {
        $this->actingAs($this->user);

        // Add product to cart
        UserItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'type' => 'cart',
        ]);

        $response = $this->get('/checkout');
        $response->assertOk();
        $response->assertViewHas('items');
    }

    /**
     * Test checkout redirects to cart if empty
     */
    public function test_checkout_redirects_when_cart_empty(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/checkout');
        $response->assertRedirect('/cart');
    }

    /**
     * Test user can apply valid coupon
     */
    public function test_user_can_apply_valid_coupon(): void
    {
        $this->actingAs($this->user);

        $coupon = Coupon::factory()->create([
            'code' => 'SAVE10',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'is_active' => true,
            'valid_until' => now()->addDays(7),
            'min_order_amount' => 0,
            'usage_limit' => null,
            'times_used' => 0,
        ]);

        // Add item to cart
        UserItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'type' => 'cart',
        ]);

        $response = $this->post('/checkout/coupon', [
            'coupon_code' => 'SAVE10',
        ]);

        $this->assertNotNull(session('coupon'));
        $this->assertEquals('SAVE10', session('coupon.code'));
    }

    /**
     * Test coupon validation fails for expired coupon
     */
    public function test_expired_coupon_cannot_be_applied(): void
    {
        $this->actingAs($this->user);

        $coupon = Coupon::factory()->create([
            'code' => 'EXPIRED',
            'is_active' => true,
            'valid_until' => now()->subDays(1),
        ]);

        $response = $this->post('/checkout/coupon', [
            'coupon_code' => 'EXPIRED',
        ]);

        $response->assertSessionHas('error');
        $this->assertNull(session('coupon'));
    }

    /**
     * Test coupon validation fails when usage limit exceeded
     */
    public function test_coupon_with_exceeded_usage_limit_cannot_be_applied(): void
    {
        $this->actingAs($this->user);

        $coupon = Coupon::factory()->create([
            'code' => 'LIMITED',
            'is_active' => true,
            'valid_until' => now()->addDays(7),
            'usage_limit' => 5,
            'times_used' => 5, // Limit reached
        ]);

        $response = $this->post('/checkout/coupon', [
            'coupon_code' => 'LIMITED',
        ]);

        $response->assertSessionHas('error');
    }

    /**
     * Test user can place order with bank transfer payment
     */
    public function test_user_can_place_order_with_bank_transfer(): void
    {
        $this->actingAs($this->user);

        // Add item to cart
        $cartItem = UserItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'type' => 'cart',
        ]);

        $response = $this->post('/checkout/process', [
            'address_line' => '123 Main Street',
            'city' => 'London',
            'phone' => '01234567890',
            'payment_method' => 'Bank Transfer',
            'items' => [$cartItem->id],
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'Bank Transfer',
        ]);

        // Verify stock was decremented
        $this->assertEquals(98, $this->product->fresh()->stock);
    }

    /**
     * Test checkout fails when stock is insufficient
     */
    public function test_checkout_fails_with_insufficient_stock(): void
    {
        $this->actingAs($this->user);

        $lowStockProduct = Product::factory()->create(['stock' => 1]);

        $cartItem = UserItem::create([
            'user_id' => $this->user->id,
            'product_id' => $lowStockProduct->id,
            'quantity' => 5, // More than available
            'type' => 'cart',
        ]);

        $response = $this->post('/checkout/process', [
            'address_line' => '123 Main Street',
            'city' => 'London',
            'phone' => '01234567890',
            'payment_method' => 'Bank Transfer',
            'items' => [$cartItem->id],
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('orders', [
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test checkout fails for under-16 user buying age-restricted product
     */
    public function test_under_16_user_cannot_buy_age_restricted_product(): void
    {
        $underAgeUser = User::factory()->create([
            'dob' => now()->subYears(15),
        ]);

        $ageRestrictedProduct = Product::factory()->create([
            'stock' => 100,
            'is_age_restricted' => true,
        ]);

        $this->actingAs($underAgeUser);

        $cartItem = UserItem::create([
            'user_id' => $underAgeUser->id,
            'product_id' => $ageRestrictedProduct->id,
            'quantity' => 1,
            'type' => 'cart',
        ]);

        $response = $this->post('/checkout/process', [
            'address_line' => '123 Main Street',
            'city' => 'London',
            'phone' => '01234567890',
            'payment_method' => 'Bank Transfer',
            'items' => [$cartItem->id],
        ]);

        $response->assertSessionHas('error');
    }

    /**
     * Test cart is cleared after successful order
     */
    public function test_cart_items_are_removed_after_checkout(): void
    {
        $this->actingAs($this->user);

        $cartItem = UserItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'type' => 'cart',
        ]);

        $this->assertTrue(
            UserItem::where('user_id', $this->user->id)
                ->where('type', 'cart')
                ->exists()
        );

        $this->post('/checkout/process', [
            'address_line' => '123 Main Street',
            'city' => 'London',
            'phone' => '01234567890',
            'payment_method' => 'Bank Transfer',
            'items' => [$cartItem->id],
        ]);

        $this->assertFalse(
            UserItem::where('user_id', $this->user->id)
                ->where('type', 'cart')
                ->exists()
        );
    }

    /**
     * Test coupon is used after checkout
     */
    public function test_coupon_times_used_incremented_after_checkout(): void
    {
        $this->actingAs($this->user);

        $coupon = Coupon::factory()->create([
            'code' => 'SAVE10',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'is_active' => true,
            'valid_until' => now()->addDays(7),
            'min_order_amount' => 0,
            'times_used' => 0,
        ]);

        session(['coupon' => [
            'code' => $coupon->code,
            'discount' => 3.00,
            'id' => $coupon->id,
        ]]);

        $cartItem = UserItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'type' => 'cart',
        ]);

        $this->post('/checkout/process', [
            'address_line' => '123 Main Street',
            'city' => 'London',
            'phone' => '01234567890',
            'payment_method' => 'Bank Transfer',
            'items' => [$cartItem->id],
        ]);

        $this->assertEquals(1, $coupon->fresh()->times_used);
    }

    /**
     * Test race condition prevention - stock lock
     */
    public function test_concurrent_checkout_does_not_oversell(): void
    {
        $limitedProduct = Product::factory()->create(['stock' => 1]);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Both users add the same limited product to cart
        UserItem::create([
            'user_id' => $user1->id,
            'product_id' => $limitedProduct->id,
            'quantity' => 1,
            'type' => 'cart',
        ]);

        UserItem::create([
            'user_id' => $user2->id,
            'product_id' => $limitedProduct->id,
            'quantity' => 1,
            'type' => 'cart',
        ]);

        // First checkout should succeed
        $this->actingAs($user1);
        $cartItem1 = UserItem::where('user_id', $user1->id)->first();

        $response1 = $this->post('/checkout/process', [
            'address_line' => '123 Main Street',
            'city' => 'London',
            'phone' => '01234567890',
            'payment_method' => 'Bank Transfer',
            'items' => [$cartItem1->id],
        ]);

        // Second checkout should fail (no stock left)
        $this->actingAs($user2);
        $cartItem2 = UserItem::where('user_id', $user2->id)->first();

        $response2 = $this->post('/checkout/process', [
            'address_line' => '123 Main Street',
            'city' => 'London',
            'phone' => '01234567890',
            'payment_method' => 'Bank Transfer',
            'items' => [$cartItem2->id],
        ]);

        // Verify stock is 0 and only one order was created
        $this->assertEquals(0, $limitedProduct->fresh()->stock);
        $this->assertEquals(1, Order::count());
    }
}
