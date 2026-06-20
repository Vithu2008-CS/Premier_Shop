<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\ReturnRequestItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReturnRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $this->adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'is_staff' => true,
        ]);

        $this->customerRole = Role::create([
            'name' => 'customer',
            'display_name' => 'Customer',
            'is_staff' => false,
        ]);

        // Create users
        $this->admin = User::factory()->create([
            'role_id' => $this->adminRole->id,
        ]);

        $this->customer = User::factory()->create([
            'role_id' => $this->customerRole->id,
        ]);
    }

    public function test_admin_can_view_returns_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.returns.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.returns.index');
    }

    public function test_guest_cannot_view_returns_index()
    {
        $response = $this->get(route('admin.returns.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_customer_cannot_view_returns_index()
    {
        $response = $this->actingAs($this->customer)->get(route('admin.returns.index'));
        $response->assertStatus(403); // or redirect if middleware acts differently, but typically admin middleware aborts with 403 or redirects
    }

    public function test_admin_can_view_return_request()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'PS-RETURNS-444',
            'subtotal' => 100.00,
            'total' => 110.00,
            'status' => 'delivered',
            'shipping_address' => ['address_line' => '123 Test St', 'city' => 'London', 'phone' => '07123456789'],
            'payment_status' => 'completed',
        ]);

        $return = ReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => $this->customer->id,
            'status' => 'pending',
            'reason' => 'Defective',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.returns.show', $return));
        $response->assertStatus(200);
        $response->assertViewIs('admin.returns.show');
        $response->assertSee($return->reason);
    }

    public function test_guest_cannot_view_return_request()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'PS-RETURNS-555',
            'subtotal' => 100.00,
            'total' => 110.00,
            'status' => 'delivered',
            'shipping_address' => ['address_line' => '123 Test St', 'city' => 'London', 'phone' => '07123456789'],
            'payment_status' => 'completed',
        ]);

        $return = ReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => $this->customer->id,
            'status' => 'pending',
            'reason' => 'Defective',
        ]);

        $response = $this->get(route('admin.returns.show', $return));
        $response->assertRedirect(route('login'));
    }

    public function test_customer_cannot_view_return_request()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'PS-RETURNS-666',
            'subtotal' => 100.00,
            'total' => 110.00,
            'status' => 'delivered',
            'shipping_address' => ['address_line' => '123 Test St', 'city' => 'London', 'phone' => '07123456789'],
            'payment_status' => 'completed',
        ]);

        $return = ReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => $this->customer->id,
            'status' => 'pending',
            'reason' => 'Defective',
        ]);

        $response = $this->actingAs($this->customer)->get(route('admin.returns.show', $return));
        $response->assertStatus(403);
    }

    public function test_admin_can_delete_return_request()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'PS-RETURNS-111',
            'subtotal' => 50.00,
            'total' => 55.00,
            'status' => 'delivered',
            'shipping_address' => ['address_line' => '123 Test St', 'city' => 'London', 'phone' => '07123456789'],
            'payment_status' => 'completed',
        ]);

        $return = ReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => $this->customer->id,
            'status' => 'pending',
            'reason' => 'Defective',
            'customer_note' => 'The screen was cracked.',
        ]);

        $this->assertDatabaseHas('return_requests', [
            'id' => $return->id,
        ]);

        // Execute delete request as Admin
        $response = $this->actingAs($this->admin)->delete(route('admin.returns.destroy', $return));

        $response->assertRedirect(route('admin.returns.index'));
        $response->assertSessionHas('success', 'Return request deleted successfully.');

        $this->assertDatabaseMissing('return_requests', [
            'id' => $return->id,
        ]);
    }

    public function test_guest_cannot_delete_return_request()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'PS-RETURNS-222',
            'subtotal' => 30.00,
            'total' => 33.00,
            'status' => 'delivered',
            'shipping_address' => ['address_line' => '123 Test St', 'city' => 'London', 'phone' => '07123456789'],
            'payment_status' => 'completed',
        ]);

        $return = ReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => $this->customer->id,
            'status' => 'pending',
            'reason' => 'Wrong Item',
        ]);

        // Unauthenticated request should redirect to login
        $response = $this->delete(route('admin.returns.destroy', $return));
        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('return_requests', [
            'id' => $return->id,
        ]);
    }

    public function test_customer_cannot_delete_return_request()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'PS-RETURNS-333',
            'subtotal' => 30.00,
            'total' => 33.00,
            'status' => 'delivered',
            'shipping_address' => ['address_line' => '123 Test St', 'city' => 'London', 'phone' => '07123456789'],
            'payment_status' => 'completed',
        ]);

        $return = ReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => $this->customer->id,
            'status' => 'pending',
            'reason' => 'Wrong Item',
        ]);

        $response = $this->actingAs($this->customer)->delete(route('admin.returns.destroy', $return));
        $response->assertStatus(403);

        $this->assertDatabaseHas('return_requests', [
            'id' => $return->id,
        ]);
    }

    // ── Update action: status transitions, stock consistency, refunds ────────

    /** Create a pending return with one item (qty 2) against a stock-5 product. */
    private function makePendingReturn(int $stock = 5, int $returnQty = 2): array
    {
        $product = Product::factory()->create(['stock' => $stock]);

        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'PS-RETURNS-UPD-'.uniqid(),
            'subtotal' => 100.00,
            'total' => 110.00,
            'status' => 'delivered',
            'shipping_address' => ['address_line' => '123 Test St', 'city' => 'London', 'phone' => '07123456789'],
            'payment_status' => 'completed',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $returnQty,
            'price' => 10.00,
        ]);

        $return = ReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => $this->customer->id,
            'status' => 'pending',
            'reason' => 'Defective',
        ]);

        ReturnRequestItem::create([
            'return_request_id' => $return->id,
            'order_item_id' => $orderItem->id,
            'quantity' => $returnQty,
        ]);

        return [$return, $product];
    }

    public function test_approving_return_restores_stock_and_notifies_customer()
    {
        [$return, $product] = $this->makePendingReturn(stock: 5, returnQty: 2);

        $response = $this->actingAs($this->admin)->put(route('admin.returns.update', $return), [
            'status' => 'approved',
        ]);

        $response->assertRedirect(route('admin.returns.show', $return));
        $this->assertEquals(7, $product->fresh()->stock);
        $this->assertEquals('approved', $return->fresh()->status);
        $this->assertDatabaseHas('app_notifications', ['user_id' => $this->customer->id]);
    }

    public function test_approving_twice_restores_stock_only_once()
    {
        [$return, $product] = $this->makePendingReturn(stock: 5, returnQty: 2);

        $this->actingAs($this->admin)->put(route('admin.returns.update', $return), ['status' => 'approved']);
        $this->actingAs($this->admin)->put(route('admin.returns.update', $return), [
            'status' => 'approved',
            'admin_note' => 'Second save, note only',
        ]);

        $this->assertEquals(7, $product->fresh()->stock);
    }

    public function test_unapproving_return_deducts_stock_back()
    {
        [$return, $product] = $this->makePendingReturn(stock: 5, returnQty: 2);

        $this->actingAs($this->admin)->put(route('admin.returns.update', $return), ['status' => 'approved']);
        $this->assertEquals(7, $product->fresh()->stock);

        $this->actingAs($this->admin)->put(route('admin.returns.update', $return), ['status' => 'rejected']);

        $this->assertEquals(5, $product->fresh()->stock);
        $this->assertEquals('rejected', $return->fresh()->status);
    }

    public function test_refunding_keeps_restored_stock_and_stores_amount()
    {
        [$return, $product] = $this->makePendingReturn(stock: 5, returnQty: 2);

        $this->actingAs($this->admin)->put(route('admin.returns.update', $return), ['status' => 'approved']);
        $this->actingAs($this->admin)->put(route('admin.returns.update', $return), [
            'status' => 'refunded',
            'refund_amount' => 20.00,
        ]);

        $this->assertEquals(7, $product->fresh()->stock);
        $this->assertEquals('refunded', $return->fresh()->status);
        $this->assertEquals(20.00, (float) $return->fresh()->refund_amount);
    }

    public function test_refunded_status_requires_refund_amount()
    {
        [$return] = $this->makePendingReturn();
        $return->update(['status' => 'approved']);

        $response = $this->actingAs($this->admin)->put(route('admin.returns.update', $return), [
            'status' => 'refunded',
        ]);

        $response->assertSessionHasErrors('refund_amount');
        $this->assertEquals('approved', $return->fresh()->status);
    }

    public function test_pending_cannot_jump_straight_to_refunded()
    {
        [$return, $product] = $this->makePendingReturn(stock: 5, returnQty: 2);

        $response = $this->actingAs($this->admin)->put(route('admin.returns.update', $return), [
            'status' => 'refunded',
            'refund_amount' => 20.00,
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertEquals('pending', $return->fresh()->status);
        $this->assertEquals(5, $product->fresh()->stock);
    }

    public function test_refunded_is_terminal()
    {
        [$return] = $this->makePendingReturn();
        $return->update(['status' => 'refunded', 'refund_amount' => 20.00]);

        $response = $this->actingAs($this->admin)->put(route('admin.returns.update', $return), [
            'status' => 'pending',
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertEquals('refunded', $return->fresh()->status);
    }

    public function test_invalid_status_value_rejected()
    {
        [$return] = $this->makePendingReturn();

        $response = $this->actingAs($this->admin)->put(route('admin.returns.update', $return), [
            'status' => 'bogus',
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertEquals('pending', $return->fresh()->status);
    }

    public function test_customer_cannot_update_return()
    {
        [$return, $product] = $this->makePendingReturn(stock: 5, returnQty: 2);

        $response = $this->actingAs($this->customer)->put(route('admin.returns.update', $return), [
            'status' => 'approved',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('pending', $return->fresh()->status);
        $this->assertEquals(5, $product->fresh()->stock);
    }
}
