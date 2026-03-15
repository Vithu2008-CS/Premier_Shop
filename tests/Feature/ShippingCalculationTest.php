<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\UserItem;
use App\Models\Setting;

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

        // Mock Google Maps API - return flat rate for all addresses
        \Illuminate\Support\Facades\Http::fake([
            'maps.googleapis.com/*' => \Illuminate\Support\Facades\Http::response([
                'status' => 'ZERO_RESULTS',
                'rows' => [['elements' => [['status' => 'ZERO_RESULTS']]]]
            ]),
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
}