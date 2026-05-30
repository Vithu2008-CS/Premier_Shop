<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
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

        // Create admin user
        $this->admin = User::factory()->create([
            'role_id' => $this->adminRole->id,
        ]);
    }

    public function test_admin_can_access_omni_search_and_get_results()
    {
        // 1. Setup Product
        $product = Product::factory()->create([
            'name' => 'Signature Apple Crisp',
            'barcode' => 'APPLE-CRISP-999',
            'price' => 12.50,
            'stock' => 15,
        ]);

        // 2. Setup Customer & Order
        $customer = User::factory()->create([
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
            'role_id' => $this->customerRole->id,
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'order_number' => 'PS-ALICE-123',
            'subtotal' => 85.00,
            'total' => 85.00,
            'status' => 'pending',
            'shipping_address' => ['address_line' => '123 Test St', 'city' => 'London', 'phone' => '07123456789'],
            'payment_status' => 'pending',
        ]);

        // 3. Make AJAX Search request as Admin for "Alice"
        $response = $this->actingAs($this->admin)->json('GET', route('admin.omniSearch', ['q' => 'Alice']));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'products',
            'orders',
            'customers',
        ]);

        // Orders and Customers should match "Alice"
        $data = $response->json();
        $this->assertCount(0, $data['products']);
        $this->assertCount(1, $data['orders']);
        $this->assertCount(1, $data['customers']);

        $this->assertEquals('PS-ALICE-123', $data['orders'][0]['order_number']);
        $this->assertEquals('Alice Smith', $data['orders'][0]['customer']);
        $this->assertEquals('Alice Smith', $data['customers'][0]['name']);
        $this->assertEquals('alice@example.com', $data['customers'][0]['email']);

        // 4. Search for "Apple"
        $response = $this->actingAs($this->admin)->json('GET', route('admin.omniSearch', ['q' => 'Apple']));
        $data = $response->json();
        $this->assertCount(1, $data['products']);
        $this->assertEquals('Signature Apple Crisp', $data['products'][0]['name']);
        $this->assertEquals('12.50', $data['products'][0]['price']);
    }

    public function test_omni_search_limits_results_to_five_per_category()
    {
        // Create 7 matching products
        Product::factory()->count(7)->create([
            'name' => 'Matched Product',
        ]);

        $response = $this->actingAs($this->admin)->json('GET', route('admin.omniSearch', ['q' => 'Matched']));
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertCount(5, $data['products']);
    }

    public function test_unauthenticated_cannot_access_omni_search()
    {
        $response = $this->json('GET', route('admin.omniSearch', ['q' => 'Apple']));
        $response->assertStatus(401);
    }
}
