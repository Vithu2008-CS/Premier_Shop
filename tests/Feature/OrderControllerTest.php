<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusUpdated;
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
}



