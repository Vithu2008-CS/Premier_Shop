<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Guards what lands in the settings other_settings JSON blob:
 *  - loyalty_enabled is only touched by forms that actually submit it (the
 *    shop-hours form posts to the same route and must not clobber it) and is
 *    stored as a real boolean
 *  - shop_hours only accepts real weekday keys with H:i times (the storefront
 *    footer Carbon::parse()s them on every page) and is normalised on write
 *  - loyalty numbers are stored typed (int/float, not request strings) and a
 *    zero redemption rate is rejected
 *  - checkout never burns points when a legacy zero rate is still stored
 */
class SettingsOtherSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'admin', 'display_name' => 'Administrator', 'is_staff' => true]);
        $this->admin = User::factory()->create(['role_id' => $role->id]);
    }

    // ── loyalty_enabled handling ─────────────────────────────────────────────

    public function test_saving_shop_hours_does_not_disable_loyalty(): void
    {
        Setting::create(['other_settings' => ['loyalty_enabled' => true]]);

        $this->actingAs($this->admin)
            ->post(route('admin.settings.store'), [
                'shop_hours' => ['monday' => ['open' => '09:00', 'close' => '17:00', 'closed' => '0']],
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue(Setting::first()->other_settings['loyalty_enabled']);
    }

    public function test_loyalty_toggle_stores_real_booleans(): void
    {
        // Checkbox checked → hidden 0 overridden by checkbox 1
        $this->actingAs($this->admin)->post(route('admin.settings.store'), ['loyalty_enabled' => '1']);
        $this->assertSame(true, Setting::first()->other_settings['loyalty_enabled']);

        // Checkbox unchecked → only the hidden 0 is submitted
        $this->actingAs($this->admin)->post(route('admin.settings.store'), ['loyalty_enabled' => '0']);
        $this->assertSame(false, Setting::first()->other_settings['loyalty_enabled']);
    }

    // ── shop_hours shape ─────────────────────────────────────────────────────

    public function test_unknown_day_keys_are_rejected(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.store'), [
                'shop_hours' => ['funday' => ['open' => '09:00', 'close' => '17:00']],
            ])
            ->assertSessionHasErrors('shop_hours');
    }

    public function test_malformed_times_are_rejected(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.store'), [
                'shop_hours' => ['monday' => ['open' => '25:99', 'close' => 'tea time']],
            ])
            ->assertSessionHasErrors(['shop_hours.monday.open', 'shop_hours.monday.close']);
    }

    public function test_shop_hours_are_normalised_on_write(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.store'), [
                'shop_hours' => [
                    'monday' => ['open' => '09:00', 'close' => '17:00', 'closed' => '0'],
                    'sunday' => ['closed' => '1'],
                ],
            ])
            ->assertSessionHasNoErrors();

        $stored = Setting::first()->other_settings['shop_hours'];

        $this->assertSame(['open' => '09:00', 'close' => '17:00', 'closed' => false], $stored['monday']);
        $this->assertSame(['open' => null, 'close' => null, 'closed' => true], $stored['sunday']);
    }

    // ── Loyalty numbers ──────────────────────────────────────────────────────

    public function test_loyalty_numbers_are_stored_typed(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.store'), [
                'loyalty_enabled'         => '1',
                'points_per_pound'        => '5',
                'points_redemption_value' => '0.02',
            ])
            ->assertSessionHasNoErrors();

        $other = Setting::first()->other_settings;

        $this->assertSame(5, $other['points_per_pound']);
        $this->assertSame(0.02, $other['points_redemption_value']);
    }

    public function test_zero_redemption_rate_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.store'), ['points_redemption_value' => '0'])
            ->assertSessionHasErrors('points_redemption_value');
    }

    // ── Checkout guard for legacy zero rates ─────────────────────────────────

    public function test_checkout_does_not_burn_points_when_stored_rate_is_zero(): void
    {
        // Legacy blob written before gt:0 validation existed
        Setting::create(['other_settings' => [
            'loyalty_enabled'         => true,
            'points_redemption_value' => 0,
        ]]);

        $role = Role::create(['name' => 'customer', 'display_name' => 'Customer', 'is_staff' => false]);
        $user = User::factory()->create(['role_id' => $role->id, 'loyalty_points' => 500]);

        $product = Product::factory()->create([
            'category_id' => Category::factory()->create()->id,
            'is_active'   => true,
            'price'       => 20,
            'stock'       => 10,
        ]);
        UserItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1, 'type' => 'cart']);

        $this->actingAs($user)->post(route('checkout.process'), [
            'address_line'   => '1 Test Street',
            'city'           => 'London',
            'phone'          => '07123456789',
            'payment_method' => 'Bank Transfer',
            'use_points'     => '1',
        ])->assertStatus(302);

        // Two guards proven at once: a zero redemption rate must NOT burn the
        // balance, and a bank-transfer order (payment still pending) must NOT
        // earn points until it is paid — so the balance stays exactly 500.
        $this->assertSame(500, $user->fresh()->loyalty_points);
        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'points_used' => 0]);
    }
}
