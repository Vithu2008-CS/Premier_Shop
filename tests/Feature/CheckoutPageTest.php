<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Models\UserItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke coverage for the checkout page after the storefront polish (order-summary
 * thumbnails, edit-cart link, shipping-pending hint). Guards against template
 * regressions and confirms the empty-cart redirect still holds.
 */
class CheckoutPageTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'customer', 'display_name' => 'Customer', 'is_staff' => false]);
        $this->user = User::factory()->create(['role_id' => $role->id]);
    }

    public function test_checkout_renders_with_cart_items(): void
    {
        $product = Product::factory()->create([
            'name' => 'CheckoutWidget',
            'category_id' => Category::factory()->create()->id,
            'is_active' => true,
            'stock' => 10,
        ]);

        UserItem::create([
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'type' => 'cart',
        ]);

        $this->actingAs($this->user)
            ->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('CheckoutWidget')
            ->assertSee('Order Summary')
            ->assertSee('Enter your address to calculate delivery');
    }

    public function test_empty_cart_redirects_to_cart(): void
    {
        $this->actingAs($this->user)
            ->get(route('checkout.index'))
            ->assertRedirect(route('cart.index'));
    }
}
