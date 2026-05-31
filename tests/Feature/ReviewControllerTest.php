<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
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

    public function test_admin_can_view_reviews_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reviews.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.reviews.index');
    }

    public function test_guest_cannot_view_reviews_index()
    {
        $response = $this->get(route('admin.reviews.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_customer_cannot_view_reviews_index()
    {
        $response = $this->actingAs($this->customer)->get(route('admin.reviews.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_review()
    {
        $product = Product::factory()->create();
        $review = Review::create([
            'user_id' => $this->customer->id,
            'product_id' => $product->id,
            'rating' => 5,
            'title' => 'Excellent Product',
            'comment' => 'It works perfectly and is super sleek.',
            'is_approved' => false,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reviews.show', $review));
        $response->assertStatus(200);
        $response->assertViewIs('admin.reviews.show');
        $response->assertSee('Excellent Product');
        $response->assertSee('It works perfectly and is super sleek.');
    }

    public function test_guest_cannot_view_review()
    {
        $product = Product::factory()->create();
        $review = Review::create([
            'user_id' => $this->customer->id,
            'product_id' => $product->id,
            'rating' => 4,
            'title' => 'Good',
            'comment' => 'Good product.',
            'is_approved' => false,
        ]);

        $response = $this->get(route('admin.reviews.show', $review));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_update_review()
    {
        $product = Product::factory()->create();
        $review = Review::create([
            'user_id' => $this->customer->id,
            'product_id' => $product->id,
            'rating' => 5,
            'title' => 'Excellent Product',
            'comment' => 'Great product!',
            'is_approved' => false,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.reviews.update', $review), [
            'is_approved' => 1,
            'admin_reply' => 'Thank you for your review!',
        ]);

        $response->assertRedirect(route('admin.reviews.show', $review));
        $review->refresh();
        $this->assertTrue($review->is_approved);
        $this->assertEquals('Thank you for your review!', $review->admin_reply);
    }

    public function test_admin_can_delete_review()
    {
        $product = Product::factory()->create();
        $review = Review::create([
            'user_id' => $this->customer->id,
            'product_id' => $product->id,
            'rating' => 5,
            'title' => 'Inappropriate Title',
            'comment' => 'Some comment to delete.',
            'is_approved' => false,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.reviews.destroy', $review));
        $response->assertRedirect(route('admin.reviews.index'));
        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }
}
