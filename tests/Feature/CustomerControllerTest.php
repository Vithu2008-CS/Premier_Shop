<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Models\UserItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminRole;

    protected $customerRole;

    protected $admin;

    protected $customer;

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

    public function test_admin_can_view_customers_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.customers.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.customers.index');
    }

    public function test_admin_can_filter_customers_index_by_orders()
    {
        $product = Product::factory()->create();

        // Active orders for customer
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'PS-1111',
            'subtotal' => 100.00,
            'total' => 100.00,
            'status' => 'delivered',
            'shipping_address' => ['city' => 'London'],
            'payment_status' => 'completed',
        ]);

        // Another customer with no orders
        $another = User::factory()->create(['role_id' => $this->customerRole->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.customers.index', [
            'min_orders' => 1,
        ]));

        $response->assertStatus(200);
        $response->assertSee($this->customer->name);
        $response->assertDontSee($another->name);
    }

    public function test_admin_can_filter_customers_index_by_spent()
    {
        // Customer 1 total spent £100
        $order1 = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'PS-1111',
            'subtotal' => 100.00,
            'total' => 100.00,
            'status' => 'delivered',
            'shipping_address' => ['city' => 'London'],
            'payment_status' => 'completed',
        ]);

        // Customer 2 total spent £20
        $another = User::factory()->create(['role_id' => $this->customerRole->id]);
        $order2 = Order::create([
            'user_id' => $another->id,
            'order_number' => 'PS-2222',
            'subtotal' => 20.00,
            'total' => 20.00,
            'status' => 'delivered',
            'shipping_address' => ['city' => 'London'],
            'payment_status' => 'completed',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.customers.index', [
            'min_spent' => 50,
        ]));

        $response->assertStatus(200);
        $response->assertSee($this->customer->name);
        $response->assertDontSee($another->name);
    }

    public function test_admin_can_view_customer_profile_with_purchase_sorting_filters()
    {
        $category = Category::factory()->create();
        $product1 = Product::factory()->create(['name' => 'Orange Juice']);
        $product2 = Product::factory()->create(['name' => 'Crossaint']);

        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'PS-4444',
            'subtotal' => 20.00,
            'total' => 20.00,
            'status' => 'delivered',
            'shipping_address' => ['city' => 'London'],
            'payment_status' => 'completed',
        ]);

        // Item 1: Qty 5, Price £2 (Total £10)
        $item1 = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'quantity' => 5,
            'price' => 2.00,
        ]);

        // Item 2: Qty 1, Price £20 (Total £20)
        $item2 = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => 20.00,
        ]);

        // Without filter (default sorting)
        $response = $this->actingAs($this->admin)->get(route('admin.customers.show', $this->customer));
        $response->assertStatus(200);
        $purchased = $response->viewData('purchasedItems');
        $this->assertCount(2, $purchased);

        // Sort by Most Quantity (Item 1 with Qty 5 should be first)
        $response = $this->actingAs($this->admin)->get(route('admin.customers.show', [
            'customer' => $this->customer->id,
            'purchase_sort' => 'qty_desc',
        ]));
        $response->assertStatus(200);
        $purchasedFiltered = $response->viewData('purchasedItems')->items();
        $this->assertEquals('Orange Juice', $purchasedFiltered[0]->product->name);

        // Sort by Least Quantity (Item 2 with Qty 1 should be first)
        $response = $this->actingAs($this->admin)->get(route('admin.customers.show', [
            'customer' => $this->customer->id,
            'purchase_sort' => 'qty_asc',
        ]));
        $response->assertStatus(200);
        $purchasedFiltered = $response->viewData('purchasedItems')->items();
        $this->assertEquals('Crossaint', $purchasedFiltered[0]->product->name);

        // Sort by Most Line Total (Item 2 with total £20 should be first)
        $response = $this->actingAs($this->admin)->get(route('admin.customers.show', [
            'customer' => $this->customer->id,
            'purchase_sort' => 'total_desc',
        ]));
        $response->assertStatus(200);
        $purchasedFiltered = $response->viewData('purchasedItems')->items();
        $this->assertEquals('Crossaint', $purchasedFiltered[0]->product->name);

        // Sort by Least Line Total (Item 1 with total £10 should be first)
        $response = $this->actingAs($this->admin)->get(route('admin.customers.show', [
            'customer' => $this->customer->id,
            'purchase_sort' => 'total_asc',
        ]));
        $response->assertStatus(200);
        $purchasedFiltered = $response->viewData('purchasedItems')->items();
        $this->assertEquals('Orange Juice', $purchasedFiltered[0]->product->name);
    }

    public function test_admin_can_update_customer_role_and_personalized_offer()
    {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $response = $this->actingAs($this->admin)->put(route('admin.customers.update', $this->customer), [
            'role_id' => $this->customerRole->id,
            'offer_discount_percentage' => 15.00,
            'offer_scope' => 'selected',
            'offer_product_ids' => [$product1->id, $product2->id],
        ]);

        $response->assertRedirect(route('admin.customers.show', $this->customer));
        $this->customer->refresh();
        $this->assertEquals(15.00, $this->customer->offer_discount_percentage);
        $this->assertEquals('selected', $this->customer->offer_scope);
        $this->assertEquals([$product1->id, $product2->id], $this->customer->offer_product_ids);
    }

    public function test_personalized_offer_is_applied_to_user_item_line_total()
    {
        $product = Product::factory()->create(['price' => 10.00]);

        $this->customer->update([
            'offer_discount_percentage' => 10.00,
            'offer_scope' => 'all',
        ]);

        $cartItem = UserItem::create([
            'user_id' => $this->customer->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'type' => 'cart',
        ]);

        // Without personalized offer, total is £20. With 10% discount, it is £18 (unit price £9.00 * 2)
        $this->assertEquals(18.00, $cartItem->line_total);
    }

    public function test_admin_can_sort_customers_by_orders_and_spent()
    {
        // Create an additional customer with custom created_at date
        $customer2 = User::factory()->create([
            'role_id' => $this->customerRole->id,
            'name' => 'Alice Customer',
            'created_at' => now()->subDays(5),
        ]);

        // Explicitly set Zack (Customer 1)'s name and created_at
        $this->customer->update([
            'name' => 'Zack Customer',
            'created_at' => now()->subDays(1),
        ]);

        // Create 2 orders for Alice (Customer 2), totaling £150
        Order::create([
            'user_id' => $customer2->id,
            'order_number' => 'PS-1111',
            'subtotal' => 75.00,
            'total' => 75.00,
            'status' => 'delivered',
            'shipping_address' => ['city' => 'London'],
            'payment_status' => 'completed',
        ]);
        Order::create([
            'user_id' => $customer2->id,
            'order_number' => 'PS-2222',
            'subtotal' => 75.00,
            'total' => 75.00,
            'status' => 'delivered',
            'shipping_address' => ['city' => 'London'],
            'payment_status' => 'completed',
        ]);

        // Create 1 order for Zack (Customer 1), totaling £200
        Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'PS-3333',
            'subtotal' => 200.00,
            'total' => 200.00,
            'status' => 'delivered',
            'shipping_address' => ['city' => 'London'],
            'payment_status' => 'completed',
        ]);

        // Sort by Newest (Zack should be first, Alice second)
        $response = $this->actingAs($this->admin)->get(route('admin.customers.index', ['sort_by' => 'newest']));
        $response->assertStatus(200);
        $customersList = $response->viewData('customers')->items();
        $this->assertEquals('Zack Customer', $customersList[0]->name);
        $this->assertEquals('Alice Customer', $customersList[1]->name);

        // Sort by Oldest (Alice should be first, Zack second)
        $response = $this->actingAs($this->admin)->get(route('admin.customers.index', ['sort_by' => 'oldest']));
        $response->assertStatus(200);
        $customersList = $response->viewData('customers')->items();
        $this->assertEquals('Alice Customer', $customersList[0]->name);
        $this->assertEquals('Zack Customer', $customersList[1]->name);

        // Sort by Most Orders (Alice has 2 orders, Zack has 1 order -> Alice should be first)
        $response = $this->actingAs($this->admin)->get(route('admin.customers.index', ['sort_by' => 'orders_desc']));
        $response->assertStatus(200);
        $customersList = $response->viewData('customers')->items();
        $this->assertEquals('Alice Customer', $customersList[0]->name);
        $this->assertEquals('Zack Customer', $customersList[1]->name);

        // Sort by Least Orders (Zack has 1 order, Alice has 2 orders -> Zack should be first)
        $response = $this->actingAs($this->admin)->get(route('admin.customers.index', ['sort_by' => 'orders_asc']));
        $response->assertStatus(200);
        $customersList = $response->viewData('customers')->items();
        $this->assertEquals('Zack Customer', $customersList[0]->name);
        $this->assertEquals('Alice Customer', $customersList[1]->name);

        // Sort by Most Spent (Zack spent £200, Alice spent £150 -> Zack should be first)
        $response = $this->actingAs($this->admin)->get(route('admin.customers.index', ['sort_by' => 'spent_desc']));
        $response->assertStatus(200);
        $customersList = $response->viewData('customers')->items();
        $this->assertEquals('Zack Customer', $customersList[0]->name);
        $this->assertEquals('Alice Customer', $customersList[1]->name);

        // Sort by Least Spent (Alice spent £150, Zack spent £200 -> Alice should be first)
        $response = $this->actingAs($this->admin)->get(route('admin.customers.index', ['sort_by' => 'spent_asc']));
        $response->assertStatus(200);
        $customersList = $response->viewData('customers')->items();
        $this->assertEquals('Alice Customer', $customersList[0]->name);
        $this->assertEquals('Zack Customer', $customersList[1]->name);
    }
}
