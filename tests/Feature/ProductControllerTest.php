<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\UserItem;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin role
        $role = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'is_staff' => true,
        ]);

        // Create an admin user
        $this->admin = User::factory()->create([
            'role_id' => $role->id,
        ]);
    }

    public function test_admin_can_store_product()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 9.99,
            'stock' => 10,
            'category_id' => $category->id,
            'product_type' => 'normal',
            'images' => json_encode(['/storage/products/test.webp']),
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 9.99,
        ]);

        $product = Product::first();
        $this->assertEquals(['/storage/products/test.webp'], $product->images);
    }

    public function test_admin_can_update_product()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'images' => ['/storage/products/old.webp'],
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product), [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'price' => 12.99,
            'stock' => 5,
            'category_id' => $category->id,
            'product_type' => 'normal',
            'images' => json_encode(['/storage/products/new.webp']),
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $product->refresh();
        $this->assertEquals('Updated Product', $product->name);
        $this->assertEquals(['/storage/products/new.webp'], $product->images);
    }

    public function test_admin_can_store_product_with_empty_images()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Test Product Empty Images',
            'description' => 'Test Description',
            'price' => 9.99,
            'stock' => 10,
            'category_id' => $category->id,
            'product_type' => 'normal',
            'images' => '',
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $product = Product::latest('id')->first();
        $this->assertEquals([], $product->images);
    }

    public function test_admin_can_store_product_with_invalid_json_images()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Test Product Invalid JSON',
            'description' => 'Test Description',
            'price' => 9.99,
            'stock' => 10,
            'category_id' => $category->id,
            'product_type' => 'normal',
            'images' => 'invalid_json_string_here_!!!',
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $product = Product::latest('id')->first();
        $this->assertEquals([], $product->images);
    }

    public function test_admin_can_search_products()
    {
        $product1 = Product::factory()->create(['name' => 'Apple Juice', 'barcode' => '12345']);
        $product2 = Product::factory()->create(['name' => 'Orange Juice', 'barcode' => '67890']);
        $product3 = Product::factory()->create(['name' => 'Whole Milk', 'barcode' => '11111']);

        // Search by name
        $response = $this->actingAs($this->admin)->get(route('admin.products.index', ['search' => 'Juice']));
        $response->assertStatus(200);
        $response->assertSee('Apple Juice');
        $response->assertSee('Orange Juice');
        $response->assertDontSee('Whole Milk');

        // Search by barcode
        $response = $this->actingAs($this->admin)->get(route('admin.products.index', ['search' => '67890']));
        $response->assertStatus(200);
        $response->assertDontSee('Apple Juice');
        $response->assertSee('Orange Juice');
        $response->assertDontSee('Whole Milk');
    }

    public function test_admin_can_get_product_suggestions()
    {
        $product1 = Product::factory()->create(['name' => 'Apple Juice', 'barcode' => '12345']);
        $product2 = Product::factory()->create(['name' => 'Orange Juice', 'barcode' => '67890']);
        $product3 = Product::factory()->create(['name' => 'Whole Milk', 'barcode' => '11111']);

        // Authenticated admin request
        $response = $this->actingAs($this->admin)->json('GET', route('admin.products.suggest', ['q' => 'Juice']));
        $response->assertStatus(200);
        $response->assertJsonCount(2);

        $data = $response->json();
        $this->assertEquals('Apple Juice', $data[0]['name']);
        $this->assertEquals('Orange Juice', $data[1]['name']);
        $this->assertEquals(route('admin.products.edit', $product1), $data[0]['url']);
    }

    public function test_unauthenticated_user_cannot_get_product_suggestions()
    {
        // Unauthenticated request should redirect to login
        $response = $this->get(route('admin.products.suggest', ['q' => 'Juice']));
        $response->assertRedirect(route('login'));
    }

    public function test_destroy_soft_deletes_product_and_preserves_order_history()
    {
        $product = Product::factory()->create();

        $order = Order::create([
            'user_id' => $this->admin->id,
            'order_number' => 'ORD-TEST-1',
            'status' => 'delivered',
            'subtotal' => 9.99,
            'shipping_cost' => 0,
            'total' => 9.99,
            'shipping_address' => ['line1' => '1 Test St', 'city' => 'London', 'postcode' => 'E1 1AA'],
        ]);
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 9.99,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertSoftDeleted('products', ['id' => $product->id]);

        // The order line must survive and still resolve its (trashed) product
        $this->assertDatabaseHas('order_items', ['id' => $orderItem->id, 'product_id' => $product->id]);
        $this->assertEquals($product->name, $orderItem->fresh()->product->name);
    }

    public function test_destroy_removes_cart_and_wishlist_entries()
    {
        $product = Product::factory()->create();

        UserItem::create(['user_id' => $this->admin->id, 'product_id' => $product->id, 'quantity' => 2, 'type' => 'cart']);

        $this->actingAs($this->admin)->delete(route('admin.products.destroy', $product));

        $this->assertDatabaseMissing('user_items', ['product_id' => $product->id]);
    }

    public function test_soft_deleted_product_slug_is_not_reused()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['name' => 'Apple Juice', 'slug' => 'apple-juice']);

        $this->actingAs($this->admin)->delete(route('admin.products.destroy', $product));

        // Same name again: trashed row still owns the unique slug, so a suffix is required
        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Apple Juice',
            'price' => 9.99,
            'stock' => 10,
            'category_id' => $category->id,
            'product_type' => 'normal',
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['slug' => 'apple-juice-2', 'deleted_at' => null]);
    }

    public function test_soft_deleted_product_hidden_from_admin_index_and_storefront()
    {
        $product = Product::factory()->create(['name' => 'Vanished Cola']);

        $this->actingAs($this->admin)->delete(route('admin.products.destroy', $product));

        $this->actingAs($this->admin)
            ->get(route('admin.products.index'))
            ->assertStatus(200)
            ->assertDontSee('Vanished Cola');

        $this->get(route('products.show', $product->slug))->assertStatus(404);
    }
}

