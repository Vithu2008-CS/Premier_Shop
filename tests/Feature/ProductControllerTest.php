<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
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
}
