<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ShippingSetting;

class ShippingCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;
    protected Cart $cart;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $category = \App\Models\Category::create(['name' => 'Test Category', 'slug' => 'test-category']);
        $this->product = Product::factory()->create(['price' => 10.00, 'stock' => 50, 'category_id' => $category->id]);

        ShippingSetting::create([
            'origin_postal_code' => 'SW1A 1AA', // London
            'free_delivery_threshold' => 100.00,
            'free_delivery_radius_miles' => 5.00,
            'surcharge_per_mile' => 1.50,
            'flat_rate_fee' => 5.99,
        ]);

        $this->cart = Cart::create(['user_id' => $this->user->id, 'subtotal' => 10.00]);
        CartItem::create([
            'cart_id' => $this->cart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => 10.00,
            'line_total' => 10.00,
        ]);

        \Illuminate\Support\Facades\Http::fake([
            'api.postcodes.io/postcodes/SW1A1AA' => \Illuminate\Support\Facades\Http::response([
                'result' => ['latitude' => 51.501009, 'longitude' => -0.141588]
            ], 200),
            'api.postcodes.io/postcodes/M11AA' => \Illuminate\Support\Facades\Http::response([
                'result' => ['latitude' => 53.480759, 'longitude' => -2.242631] // ~162 miles away
            ], 200),
            'api.postcodes.io/postcodes/W1A1AA' => \Illuminate\Support\Facades\Http::response([
                'result' => ['latitude' => 51.518561, 'longitude' => -0.143799] // < 2 miles away
            ], 200),
            '*' => \Illuminate\Support\Facades\Http::response([], 404),
        ]);
    }

    public function test_flat_rate_fallback_when_distance_is_null_or_invalid_postcode()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('checkout.calculateShipping'), [
                'postcode' => 'INVALID_POSTCODE',
            ]);

        $response->assertStatus(200);
        $this->assertEquals(5.99, $response->json('cost'));
        $this->assertEquals('Flat rate shipping.', $response->json('message'));
        $this->assertNull($response->json('distance'));
    }

    public function test_free_shipping_over_threshold()
    {
        $this->cart->update(['subtotal' => 150.00]);

        $response = $this->actingAs($this->user)
            ->postJson(route('checkout.calculateShipping'), [
                'postcode' => 'M1 1AA', // Manchester
            ]);

        $response->assertStatus(200);
        $this->assertEquals(0.00, $response->json('cost'));
        $this->assertStringContainsString('Free shipping (Over £100)', $response->json('message'));
    }

    public function test_free_local_delivery_within_radius()
    {
        // Testing with a postcode close to SW1A 1AA like W1A 1AA (BBC Broadcasting House)
        $response = $this->actingAs($this->user)
            ->postJson(route('checkout.calculateShipping'), [
                'postcode' => 'W1A 1AA',
            ]);

        $response->assertStatus(200);
        $this->assertEquals(0.00, $response->json('cost'));
        $this->assertStringContainsString('Free local delivery', $response->json('message'));
    }

    public function test_surcharge_calculation_outside_free_radius()
    {
        // Manchester is ~162 miles from London. 162 - 5 (free radius) = 157 extra miles
        // Cost: 5.99 (flat) + (157 * 1.50) = ~241.49

        $response = $this->actingAs($this->user)
            ->postJson(route('checkout.calculateShipping'), [
                'postcode' => 'M1 1AA',
            ]);

        $response->assertStatus(200);

        $cost = $response->json('cost');
        $this->assertGreaterThan(5.99, $cost); // Should be significantly more than flat rate
        $this->assertStringContainsString('Includes £1.50/mile surcharge', $response->json('message'));
    }
}
