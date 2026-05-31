<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\User;
use App\Models\Role;
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
}
