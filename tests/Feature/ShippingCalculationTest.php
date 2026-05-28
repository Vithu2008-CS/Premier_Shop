<?php

/**
 * ShippingCalculationTest — Feature tests for checkout shipping cost logic.
 * Covers: flat-rate fee, free delivery threshold, distance-based surcharge.
 * Uses RefreshDatabase; seeds a Setting row and a product in setUp().
 */

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $category = \App\Models\Category::create(['name' => 'Test Category', 'slug' => 'test-category']);
        $this->product = Product::factory()->create(['price' => 10.00, 'stock' => 50, 'category_id' => $category->id]);

        Setting::create([
            'shop_name' => 'Test Shop',
            'origin_address' => 'Buckingham Palace, London, SW1A 1AA, UK',
            'free_delivery_threshold' => 100.00,
            'free_delivery_radius_miles' => 5.00,
            'surcharge_per_mile' => 1.50,
            'flat_rate_fee' => 5.99,
        ]);

        UserItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'type' => 'cart',
        ]);

        // Smart dynamic Google Maps API mock - handles coordinate tests and fallback postcodes beautifully
        \Illuminate\Support\Facades\Http::fake([
            'maps.googleapis.com/*' => function ($request) {
                if (str_contains($request->url(), 'distancematrix/json')) {
                    $origins = $request['origins'] ?? '';
                    $destinations = $request['destinations'] ?? '';
                    
                    if (str_contains($origins, '51.5074,-0.1278')) {
                        // 8046.72 meters = 5 miles (exactly the free local delivery radius)
                        // 16093.44 meters = 10 miles (for calculation service test)
                        $meters = str_contains($destinations, 'Manchester') || str_contains($destinations, 'London') ? 8046.72 : 16093.44;
                        
                        return \Illuminate\Support\Facades\Http::response([
                            'status' => 'OK',
                            'rows' => [
                                [
                                    'elements' => [
                                        [
                                            'status' => 'OK',
                                            'distance' => [
                                                'value' => $meters,
                                            ],
                                        ]
                                    ]
                                ]
                            ],
                        ]);
                    }
                }
                
                return \Illuminate\Support\Facades\Http::response([
                    'status' => 'ZERO_RESULTS',
                    'rows' => [['elements' => [['status' => 'ZERO_RESULTS']]]],
                ]);
            }
        ]);
    }

    public function test_flat_rate_fallback_when_distance_is_null_or_invalid_postcode()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('checkout.calculateShipping'), [
                'address_line' => 'Invalid Address',
                'city' => 'INVALID_CITY',
            ]);

        $response->assertStatus(200);
        $this->assertEquals(5.99, $response->json('cost'));
        $this->assertEquals('Flat rate shipping.', $response->json('message'));
    }

    public function test_free_shipping_over_threshold()
    {
        // Update existing cart with high subtotal to test free shipping threshold
        UserItem::where('user_id', $this->user->id)->first()->update([
            'quantity' => 15,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('checkout.calculateShipping'), [
                'address_line' => 'Manchester Street',
                'city' => 'Manchester',
            ]);

        $response->assertStatus(200);
        $this->assertEquals(0.00, $response->json('cost'));
        $this->assertStringContainsString('Free shipping (Over £100.00)', $response->json('message'));
    }

    public function test_shipping_calculation_requires_address_fields()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('checkout.calculateShipping'), [
                'city' => 'London',
                // Missing address_line
            ]);

        $response->assertStatus(422); // Validation error
    }

    public function test_shipping_calculation_validates_both_address_fields()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('checkout.calculateShipping'), [
                'address_line' => '123 Main St',
                // Missing city
            ]);

        $response->assertStatus(422); // Validation error
    }

    public function test_shipping_calculation_uses_coordinates_if_set()
    {
        // Set mock API key config so services do not exit early
        config(['services.google.maps_key' => 'mocked-key']);

        // 1. Configure settings with coordinates in other_settings
        $settings = Setting::first();
        $settings->update([
            'other_settings' => [
                'origin_latitude' => 51.5074,
                'origin_longitude' => -0.1278,
            ]
        ]);

        // 2. Request shipping calculation preview (uses ShippingService in CheckoutController)
        $response = $this->actingAs($this->user)
            ->postJson(route('checkout.calculateShipping'), [
                'address_line' => '10 Downing Street',
                'city' => 'London',
            ]);

        // 3. Verify coordinates were sent as origin in ShippingService call
        \Illuminate\Support\Facades\Http::assertSent(function ($request) {
            return str_contains($request->url(), 'maps.googleapis.com/maps/api/distancematrix/json')
                && str_contains($request['origins'], '51.5074,-0.1278');
        });

        $response->assertStatus(200);
        $this->assertEquals(0.00, $response->json('cost'));
        $this->assertStringContainsString('Free local delivery', $response->json('message'));
    }

    public function test_shipping_calculation_service_uses_coordinates_directly()
    {
        // Set mock API key config so services do not exit early
        config(['services.google.maps_key' => 'mocked-key']);

        // 1. Configure settings with coordinates in other_settings
        $settings = Setting::first();
        $settings->update([
            'other_settings' => [
                'origin_latitude' => 51.5074,
                'origin_longitude' => -0.1278,
            ]
        ]);

        // 2. Use ShippingCalculationService directly
        $service = new \App\Services\ShippingCalculationService();
        $distanceMiles = $service->calculateDrivingDistance('New Street, Birmingham, UK');

        // 3. Verify coordinates were sent as origin in ShippingCalculationService call
        \Illuminate\Support\Facades\Http::assertSent(function ($request) {
            return str_contains($request->url(), 'maps.googleapis.com/maps/api/distancematrix/json')
                && str_contains($request['origins'], '51.5074,-0.1278');
        });

        $this->assertEquals(10.00, $distanceMiles);
    }
}
