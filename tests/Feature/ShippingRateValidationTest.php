<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\Setting;
use App\Models\ShippingRate;
use App\Models\User;
use App\Models\UserItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Guards shipping rate handling against bad values:
 *  - admin global rates (base fee / per-mile / per-kg) reject negative, zero
 *    and column-overflow values
 *  - settings shipping fields reject negative values
 *  - the checkout calculators clamp at zero, so rates written outside admin
 *    validation (seeders, direct SQL) can never produce a negative shipping
 *    cost that subtracts from the order total
 */
class ShippingRateValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'admin', 'display_name' => 'Administrator', 'is_staff' => true]);
        $this->admin = User::factory()->create(['role_id' => $role->id]);
    }

    // ── Admin global rates (ShippingRateController) ──────────────────────────

    public function test_negative_rate_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.shipping-rates.update'), [
                'base_connection_fee' => '-5.00',
                'per_mile_rate'       => '0.50',
                'per_kg_surcharge'    => '0.20',
            ])
            ->assertSessionHasErrors('base_connection_fee');
    }

    public function test_zero_rate_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.shipping-rates.update'), [
                'base_connection_fee' => '5.00',
                'per_mile_rate'       => '0',
                'per_kg_surcharge'    => '0.20',
            ])
            ->assertSessionHasErrors('per_mile_rate');
    }

    public function test_rate_exceeding_column_capacity_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.shipping-rates.update'), [
                'base_connection_fee' => '5.00',
                'per_mile_rate'       => '0.50',
                'per_kg_surcharge'    => '1000000', // > decimal(8,2) max of 999,999.99
            ])
            ->assertSessionHasErrors('per_kg_surcharge');
    }

    public function test_valid_rates_are_accepted(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.shipping-rates.update'), [
                'base_connection_fee' => '4.50',
                'per_mile_rate'       => '0.75',
                'per_kg_surcharge'    => '0.30',
            ])
            ->assertRedirect(route('admin.shipping-rates.index'));

        $this->assertDatabaseHas('shipping_rates', [
            'base_connection_fee' => 4.50,
            'per_mile_rate'       => 0.75,
            'per_kg_surcharge'    => 0.30,
        ]);
    }

    // ── Settings shipping fields (SettingController) ─────────────────────────

    public function test_negative_flat_rate_fee_setting_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.store'), ['flat_rate_fee' => '-3.00'])
            ->assertSessionHasErrors('flat_rate_fee');
    }

    public function test_negative_surcharge_per_mile_setting_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.store'), ['surcharge_per_mile' => '-1.50'])
            ->assertSessionHasErrors('surcharge_per_mile');
    }

    // ── Runtime clamps — negative DB rates never quote below zero ────────────

    /** Seed a customer with one £10 cart item; mock the maps API at 10 miles. */
    private function customerWithCart(): User
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Clamp Category', 'slug' => 'clamp-category']);
        $product = Product::factory()->create(['price' => 10.00, 'stock' => 50, 'category_id' => $category->id]);

        UserItem::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => 1,
            'type'       => 'cart',
        ]);

        config(['services.google.maps_key' => 'mocked-key']);
        Http::fake([
            'maps.googleapis.com/*' => Http::response([
                'status' => 'OK',
                'rows'   => [['elements' => [[
                    'status'   => 'OK',
                    'distance' => ['value' => 16093.44], // 10 miles
                ]]]],
            ]),
        ]);

        return $user;
    }

    public function test_negative_surcharge_setting_is_clamped_to_zero_in_preview(): void
    {
        // 10-mile trip, 5-mile free radius: 5 extra miles × -20.00 + 5.99 ≈ -94
        // without the clamp — written directly to bypass admin validation
        Setting::create([
            'free_delivery_threshold'    => 1000.00,
            'free_delivery_radius_miles' => 5.00,
            'surcharge_per_mile'         => -20.00,
            'flat_rate_fee'              => 5.99,
        ]);

        $response = $this->actingAs($this->customerWithCart())
            ->postJson(route('checkout.calculateShipping'), [
                'address_line' => '10 Downing Street',
                'city'         => 'London',
            ]);

        $response->assertStatus(200);
        $this->assertSame(0.0, (float) $response->json('cost'));
    }

    public function test_negative_global_rates_are_clamped_to_zero_in_dynamic_quote(): void
    {
        Setting::create(['flat_rate_fee' => 5.99]);

        // Bypasses ShippingRateController validation (e.g. direct SQL / seeder).
        // The migration seeds a default row, so update it rather than add a second.
        ShippingRate::query()->update(['base_connection_fee' => -50.00]);

        $response = $this->actingAs($this->customerWithCart())
            ->postJson(route('checkout.calculateShippingDynamic'), [
                'address_line' => '10 Downing Street',
                'city'         => 'London',
            ]);

        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(0.0, (float) $response->json('cost'));
        $this->assertSame(0.0, (float) $response->json('cost'));
    }
}
