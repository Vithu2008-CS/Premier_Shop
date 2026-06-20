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
 * Smoke coverage for the cart page after the polish (offer-accurate summary that
 * sums server line totals, discounted unit price display, savings row).
 */
class CartPageTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'customer', 'display_name' => 'Customer', 'is_staff' => false]);
        $this->user = User::factory()->create(['role_id' => $role->id]);
    }

    public function test_cart_lists_active_items_with_server_line_totals(): void
    {
        $product = Product::factory()->create([
            'name' => 'CartWidget',
            'category_id' => Category::factory()->create()->id,
            'is_active' => true,
            'stock' => 10,
        ]);
        UserItem::create([
            'user_id' => $this->user->id, 'product_id' => $product->id,
            'quantity' => 2, 'type' => 'cart',
        ]);

        $this->actingAs($this->user)
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee('CartWidget')
            ->assertSee('Order Summary')
            ->assertSee('data-line-total', false); // summary sums authoritative line totals
    }

    public function test_empty_cart_shows_empty_state(): void
    {
        $this->actingAs($this->user)
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Your cart is empty');
    }

    public function test_inactive_product_is_hidden_from_cart(): void
    {
        $product = Product::factory()->create([
            'name' => 'HiddenWidget', 'category_id' => Category::factory()->create()->id,
            'is_active' => false, 'stock' => 10,
        ]);
        UserItem::create([
            'user_id' => $this->user->id, 'product_id' => $product->id,
            'quantity' => 1, 'type' => 'cart',
        ]);

        // Only inactive item in cart → treated as empty.
        $this->actingAs($this->user)
            ->get(route('cart.index'))
            ->assertOk()
            ->assertDontSee('HiddenWidget')
            ->assertSee('Your cart is empty');
    }
}
