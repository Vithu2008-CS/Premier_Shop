<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DeliveryZone;
use App\Models\Product;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserItem;
use App\Services\DeliveryZoneService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Guards the zone-based delivery pricing engine:
 *  - zone matching by driving distance (band bounds, overlap → tightest wins)
 *  - fee resolution (fully free zone, free-over threshold, flat zone fee)
 *  - flat-rate fallback when distance is unknown or outside every zone
 *  - admin CRUD validation (band coherence, negative/overflow bounds)
 *  - the checkout AJAX quote endpoint end-to-end with a mocked Maps API
 */
class DeliveryZoneTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'admin', 'display_name' => 'Administrator', 'is_staff' => true]);
        $this->admin = User::factory()->create(['role_id' => $role->id]);
    }

    // ── Zone matching ────────────────────────────────────────────────────────

    public function test_match_for_picks_the_band_covering_the_distance(): void
    {
        $near = DeliveryZone::create(['name' => 'Near', 'min_miles' => 0, 'max_miles' => 1.5, 'delivery_fee' => 0, 'is_free' => true]);
        $town = DeliveryZone::create(['name' => 'Town', 'min_miles' => 1.5, 'max_miles' => 5, 'delivery_fee' => 2.50]);

        $this->assertTrue($near->is($near::matchFor(1.0)));
        $this->assertTrue($town->is(DeliveryZone::matchFor(3.0)));
        $this->assertNull(DeliveryZone::matchFor(10.0));
    }

    public function test_tightest_zone_wins_when_bands_overlap(): void
    {
        DeliveryZone::create(['name' => 'Wide', 'min_miles' => 0, 'max_miles' => 10, 'delivery_fee' => 4.00]);
        $tight = DeliveryZone::create(['name' => 'Tight', 'min_miles' => 0, 'max_miles' => 1.5, 'is_free' => true, 'delivery_fee' => 0]);

        $this->assertTrue($tight->is(DeliveryZone::matchFor(1.0)));
    }

    // ── Fee resolution ───────────────────────────────────────────────────────

    public function test_fee_resolution_per_zone_rule(): void
    {
        $free = new DeliveryZone(['is_free' => true, 'delivery_fee' => 9.99]);
        $threshold = new DeliveryZone(['is_free' => false, 'free_over_amount' => 20, 'delivery_fee' => 2.50]);
        $flat = new DeliveryZone(['is_free' => false, 'delivery_fee' => 3.00]);

        $this->assertSame(0.0, $free->feeFor(5.0));
        $this->assertSame(0.0, $threshold->feeFor(25.0));   // over £20 → free
        $this->assertSame(2.5, $threshold->feeFor(15.0));   // under £20 → £2.50
        $this->assertSame(0.0, $threshold->feeFor(20.0));   // exactly £20 → free
        $this->assertSame(3.0, $flat->feeFor(100.0));
    }

    // ── Fallback ─────────────────────────────────────────────────────────────

    public function test_quote_falls_back_to_flat_rate_when_distance_is_unknown(): void
    {
        Setting::create(['flat_rate_fee' => 4.50]);
        DeliveryZone::create(['name' => 'Near', 'min_miles' => 0, 'max_miles' => 5, 'delivery_fee' => 2.50]);

        $quote = app(DeliveryZoneService::class)->quote(null, 10.0);

        $this->assertSame(4.5, $quote['cost']);
        $this->assertNull($quote['zone']);
    }

    public function test_quote_falls_back_when_distance_is_outside_every_zone(): void
    {
        Setting::create(['flat_rate_fee' => 4.50]);
        DeliveryZone::create(['name' => 'Near', 'min_miles' => 0, 'max_miles' => 5, 'delivery_fee' => 2.50]);

        $quote = app(DeliveryZoneService::class)->quote(50.0, 10.0);

        $this->assertSame(4.5, $quote['cost']);
        $this->assertNull($quote['zone']);
        $this->assertStringContainsString('outside configured zones', $quote['message']);
    }

    // ── Admin CRUD ───────────────────────────────────────────────────────────

    public function test_admin_can_create_a_zone(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.delivery-zones.store'), [
                'name' => 'Town Centre',
                'min_miles' => '0',
                'max_miles' => '1.5',
                'free_over_amount' => '20',
                'delivery_fee' => '2.50',
            ])
            ->assertRedirect(route('admin.delivery-zones.index'));

        $this->assertDatabaseHas('delivery_zones', [
            'name' => 'Town Centre',
            'max_miles' => 1.5,
            'delivery_fee' => 2.50,
        ]);
    }

    public function test_zone_band_must_end_after_it_starts(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.delivery-zones.store'), [
                'name' => 'Backwards',
                'min_miles' => '5',
                'max_miles' => '1.5',
            ])
            ->assertSessionHasErrors('max_miles');

        $this->assertSame(0, DeliveryZone::count());
    }

    public function test_negative_fee_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.delivery-zones.store'), [
                'name' => 'Negative',
                'min_miles' => '0',
                'max_miles' => '5',
                'delivery_fee' => '-2.50',
            ])
            ->assertSessionHasErrors('delivery_fee');
    }

    public function test_admin_can_update_a_zone(): void
    {
        $zone = DeliveryZone::create(['name' => 'Old Name', 'min_miles' => 0, 'max_miles' => 5, 'delivery_fee' => 2.50]);

        $this->actingAs($this->admin)
            ->put(route('admin.delivery-zones.update', $zone), [
                'name' => 'New Name',
                'min_miles' => '0',
                'max_miles' => '6',
                'delivery_fee' => '3.00',
            ])
            ->assertRedirect(route('admin.delivery-zones.index'));

        $this->assertDatabaseHas('delivery_zones', ['id' => $zone->id, 'name' => 'New Name', 'max_miles' => 6]);
    }

    public function test_admin_can_delete_a_zone(): void
    {
        $zone = DeliveryZone::create(['name' => 'Doomed', 'min_miles' => 0, 'max_miles' => 5, 'delivery_fee' => 2.50]);

        $this->actingAs($this->admin)
            ->delete(route('admin.delivery-zones.destroy', $zone))
            ->assertRedirect(route('admin.delivery-zones.index'));

        $this->assertDatabaseMissing('delivery_zones', ['id' => $zone->id]);
    }

    public function test_zone_list_shows_zones(): void
    {
        DeliveryZone::create(['name' => 'Town Centre', 'min_miles' => 0, 'max_miles' => 1.5, 'is_free' => true, 'delivery_fee' => 0]);

        $this->actingAs($this->admin)
            ->get(route('admin.delivery-zones.index'))
            ->assertOk()
            ->assertSee('Town Centre');
    }

    // ── Checkout quote endpoint ──────────────────────────────────────────────

    /** Customer with a cart at the given subtotal; Maps API mocked at 10 miles. */
    private function customerWithCart(float $subtotal): User
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Zone Category', 'slug' => 'zone-category']);
        $product = Product::factory()->create(['price' => $subtotal, 'stock' => 50, 'category_id' => $category->id]);

        UserItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'type' => 'cart',
        ]);

        config(['services.google.maps_key' => 'mocked-key']);
        Http::fake([
            'maps.googleapis.com/*' => Http::response([
                'status' => 'OK',
                'rows' => [['elements' => [[
                    'status' => 'OK',
                    'distance' => ['value' => 16093.44], // 10 miles
                ]]]],
            ]),
        ]);

        return $user;
    }

    public function test_quote_endpoint_charges_zone_fee_under_the_threshold(): void
    {
        DeliveryZone::create(['name' => 'Suburbs', 'min_miles' => 0, 'max_miles' => 15, 'free_over_amount' => 20, 'delivery_fee' => 2.50]);

        $response = $this->actingAs($this->customerWithCart(10.00))
            ->postJson(route('checkout.calculateShippingDynamic'), [
                'address_line' => '10 Downing Street',
                'city' => 'London',
            ]);

        $response->assertOk();
        $this->assertSame(2.5, (float) $response->json('cost'));
        $this->assertSame(10.0, (float) $response->json('distance_miles'));
    }

    public function test_quote_endpoint_is_free_over_the_threshold(): void
    {
        DeliveryZone::create(['name' => 'Suburbs', 'min_miles' => 0, 'max_miles' => 15, 'free_over_amount' => 20, 'delivery_fee' => 2.50]);

        $response = $this->actingAs($this->customerWithCart(30.00))
            ->postJson(route('checkout.calculateShippingDynamic'), [
                'address_line' => '10 Downing Street',
                'city' => 'London',
            ]);

        $response->assertOk();
        $this->assertSame(0.0, (float) $response->json('cost'));
        $this->assertStringContainsString('Free delivery', $response->json('message'));
    }

    public function test_quote_endpoint_honours_a_fully_free_zone(): void
    {
        DeliveryZone::create(['name' => 'Local', 'min_miles' => 0, 'max_miles' => 15, 'is_free' => true, 'delivery_fee' => 0]);

        $response = $this->actingAs($this->customerWithCart(3.00))
            ->postJson(route('checkout.calculateShippingDynamic'), [
                'address_line' => '10 Downing Street',
                'city' => 'London',
            ]);

        $response->assertOk();
        $this->assertSame(0.0, (float) $response->json('cost'));
    }

    public function test_quote_endpoint_falls_back_to_flat_rate_without_zones(): void
    {
        // No zones, no settings row → default £5.99 flat fallback
        $response = $this->actingAs($this->customerWithCart(10.00))
            ->postJson(route('checkout.calculateShippingDynamic'), [
                'address_line' => '10 Downing Street',
                'city' => 'London',
            ]);

        $response->assertOk();
        $this->assertSame(5.99, (float) $response->json('cost'));
    }

    public function test_quote_endpoint_handles_an_empty_cart(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('checkout.calculateShippingDynamic'), [
                'address_line' => '10 Downing Street',
                'city' => 'London',
            ]);

        $response->assertOk();
        $this->assertSame(0.0, (float) $response->json('cost'));
        $this->assertSame('Your cart is empty.', $response->json('message'));
    }

    // ── Distance service origin (kept from the old shipping test suite) ──────

    public function test_distance_service_uses_configured_coordinates_as_origin(): void
    {
        config(['services.google.maps_key' => 'mocked-key']);

        Setting::create([
            'other_settings' => ['origin_latitude' => 51.5074, 'origin_longitude' => -0.1278],
        ]);

        Http::fake([
            'maps.googleapis.com/*' => Http::response([
                'status' => 'OK',
                'rows' => [['elements' => [[
                    'status' => 'OK',
                    'distance' => ['value' => 16093.44],
                ]]]],
            ]),
        ]);

        $miles = (new \App\Services\ShippingCalculationService)->calculateDrivingDistance('New Street, Birmingham, UK');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'distancematrix/json')
                && str_contains($request['origins'], '51.5074,-0.1278');
        });

        $this->assertEquals(10.00, $miles);
    }
}
