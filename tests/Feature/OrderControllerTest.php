<?php

namespace Tests\Feature;

use App\Mail\OrderStatusUpdated;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'is_staff' => true,
        ]);

        $this->admin = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $this->customer = User::factory()->create();
    }

    public function test_admin_can_update_order_to_shipped_and_sends_email()
    {
        Mail::fake();

        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'PS-123456',
            'status' => 'pending',
            'subtotal' => 10.00,
            'total' => 10.00,
            'shipping_address' => ['address_line' => '123 Test St', 'city' => 'London', 'phone' => '07123456789'],
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.orders.updateStatus', $order), [
            'status' => 'shipped',
            'payment_status' => 'completed',
        ]);

        $response->assertRedirect();
        $order->refresh();

        $this->assertEquals('shipped', $order->status);
        $this->assertEquals('completed', $order->payment_status);
        $this->assertNotNull($order->shipped_date);

        Mail::assertSent(OrderStatusUpdated::class, function ($mail) use ($order) {
            return $mail->order->id === $order->id && $mail->hasTo($this->customer->email);
        });
    }

    public function test_order_status_updated_mailable_renders()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'PS-123456',
            'status' => 'shipped',
            'subtotal' => 10.00,
            'total' => 10.00,
            'shipping_address' => ['address_line' => '123 Test St', 'city' => 'London', 'phone' => '07123456789'],
            'payment_status' => 'pending',
            'shipped_date' => now(),
        ]);

        $mailable = new OrderStatusUpdated($order);
        $html = $mailable->render();
        $this->assertNotEmpty($html);
    }

    public function test_admin_can_update_order_status_without_payment_status()
    {
        Mail::fake();

        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'PS-123456',
            'status' => 'pending',
            'subtotal' => 10.00,
            'total' => 10.00,
            'shipping_address' => ['address_line' => '123 Test St', 'city' => 'London', 'phone' => '07123456789'],
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.orders.updateStatus', $order), [
            'status' => 'shipped',
        ]);

        $response->assertRedirect();
        $order->refresh();

        $this->assertEquals('shipped', $order->status);
        $this->assertEquals('pending', $order->payment_status);
        $this->assertNotNull($order->shipped_date);

        Mail::assertSent(OrderStatusUpdated::class, function ($mail) use ($order) {
            return $mail->order->id === $order->id && $mail->hasTo($this->customer->email);
        });
    }

    public function test_order_status_updated_mailable_renders_with_null_shipped_date_when_status_is_shipped()
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'PS-123456',
            'status' => 'shipped',
            'subtotal' => 10.00,
            'total' => 10.00,
            'shipping_address' => ['address_line' => '123 Test St', 'city' => 'London', 'phone' => '07123456789'],
            'payment_status' => 'pending',
            'shipped_date' => null,
        ]);

        $mailable = new OrderStatusUpdated($order);
        $html = $mailable->render();
        $this->assertNotEmpty($html);
    }

    private function makeOrder(): Order
    {
        return Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'PS-'.strtoupper(uniqid()),
            'status' => 'processing',
            'subtotal' => 20.00,
            'total' => 20.00,
            'shipping_address' => ['address_line' => '456 Delivery Rd', 'city' => 'Manchester', 'phone' => '07123456789'],
            'payment_status' => 'completed',
        ]);
    }

    public function test_admin_can_assign_on_duty_driver()
    {
        $driverRole = Role::create(['name' => 'driver', 'display_name' => 'Driver', 'is_staff' => true]);
        $driver = User::factory()->create(['role_id' => $driverRole->id, 'is_on_duty' => true]);
        $order = $this->makeOrder();

        $response = $this->actingAs($this->admin)->post(route('admin.orders.assignDriver', $order), [
            'driver_id' => $driver->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertEquals($driver->id, $order->fresh()->driver_id);
    }

    public function test_assigning_non_driver_user_is_rejected()
    {
        $order = $this->makeOrder();

        // $this->customer has no driver role and is not on duty
        $response = $this->actingAs($this->admin)->post(route('admin.orders.assignDriver', $order), [
            'driver_id' => $this->customer->id,
        ]);

        $response->assertSessionHasErrors('driver_id');
        $this->assertNull($order->fresh()->driver_id);
    }

    public function test_assigning_off_duty_driver_is_rejected()
    {
        $driverRole = Role::create(['name' => 'driver', 'display_name' => 'Driver', 'is_staff' => true]);
        $driver = User::factory()->create(['role_id' => $driverRole->id, 'is_on_duty' => false]);
        $order = $this->makeOrder();

        $response = $this->actingAs($this->admin)->post(route('admin.orders.assignDriver', $order), [
            'driver_id' => $driver->id,
        ]);

        $response->assertSessionHasErrors('driver_id');
        $this->assertNull($order->fresh()->driver_id);
    }

    public function test_admin_can_unassign_driver()
    {
        $driverRole = Role::create(['name' => 'driver', 'display_name' => 'Driver', 'is_staff' => true]);
        $driver = User::factory()->create(['role_id' => $driverRole->id, 'is_on_duty' => true]);
        $order = $this->makeOrder();
        $order->update(['driver_id' => $driver->id]);

        $response = $this->actingAs($this->admin)->post(route('admin.orders.assignDriver', $order), [
            'driver_id' => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertNull($order->fresh()->driver_id);
    }

    public function test_customer_order_detail_view_shows_assigned_driver()
    {
        $driver = User::factory()->create([
            'name' => 'John Driver',
            'phone' => '07888888888',
        ]);

        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'PS-987654',
            'status' => 'processing',
            'subtotal' => 20.00,
            'total' => 20.00,
            'shipping_address' => ['address_line' => '456 Delivery Rd', 'city' => 'Manchester', 'phone' => '07123456789'],
            'payment_status' => 'completed',
            'driver_id' => $driver->id,
        ]);

        $response = $this->actingAs($this->customer)->get(route('orders.show', $order));

        $response->assertStatus(200);
        $response->assertSee('John Driver');
        $response->assertSee('Assigned Driver');
    }
}
