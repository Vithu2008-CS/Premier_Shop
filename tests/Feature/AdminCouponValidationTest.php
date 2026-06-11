<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Guards coupon validation in the admin CRUD:
 *  - code uniqueness is case-insensitive ("sale10" collides with "SALE10"
 *    instead of slipping past validation into a DB unique-constraint 500)
 *  - the valid_from/valid_until range must be coherent
 *  - discount bounds: percentage coupons are capped at 100%, zero-value
 *    coupons are rejected, and the discount can never exceed the subtotal
 */
class AdminCouponValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'admin', 'display_name' => 'Administrator', 'is_staff' => true]);
        $this->admin = User::factory()->create(['role_id' => $role->id]);
    }

    /** Minimal valid payload; override per test. */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'code'           => 'SAVE10',
            'discount_type'  => 'percentage',
            'discount_value' => '10',
        ], $overrides);
    }

    // ── Code uniqueness ──────────────────────────────────────────────────────

    public function test_code_is_stored_uppercase(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.coupons.store'), $this->payload(['code' => 'save10']))
            ->assertRedirect(route('admin.coupons.index'));

        $this->assertDatabaseHas('coupons', ['code' => 'SAVE10']);
    }

    public function test_duplicate_code_differing_only_by_case_is_rejected(): void
    {
        Coupon::create(['code' => 'SALE10', 'discount_type' => 'percentage', 'discount_value' => 10]);

        $this->actingAs($this->admin)
            ->post(route('admin.coupons.store'), $this->payload(['code' => 'sale10']))
            ->assertSessionHasErrors('code');

        $this->assertSame(1, Coupon::count());
    }

    public function test_coupon_can_be_updated_without_changing_its_code(): void
    {
        $coupon = Coupon::create(['code' => 'SUMMER10', 'discount_type' => 'percentage', 'discount_value' => 10]);

        $this->actingAs($this->admin)
            ->put(route('admin.coupons.update', $coupon), $this->payload([
                'code'           => 'summer10', // same code, different case
                'discount_value' => '15',
            ]))
            ->assertRedirect(route('admin.coupons.index'));

        $this->assertDatabaseHas('coupons', ['id' => $coupon->id, 'code' => 'SUMMER10', 'discount_value' => 15]);
    }

    // ── Date range ───────────────────────────────────────────────────────────

    public function test_valid_until_before_valid_from_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.coupons.store'), $this->payload([
                'valid_from'  => '2026-07-01 00:00:00',
                'valid_until' => '2026-06-01 00:00:00',
            ]))
            ->assertSessionHasErrors('valid_until');

        $this->assertSame(0, Coupon::count());
    }

    public function test_valid_until_without_valid_from_is_accepted(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.coupons.store'), $this->payload([
                'valid_until' => '2027-01-01 00:00:00',
            ]))
            ->assertRedirect(route('admin.coupons.index'));

        $this->assertSame(1, Coupon::count());
    }

    // ── Discount bounds ──────────────────────────────────────────────────────

    public function test_percentage_discount_over_100_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.coupons.store'), $this->payload(['discount_value' => '150']))
            ->assertSessionHasErrors('discount_value');

        $this->assertSame(0, Coupon::count());
    }

    public function test_percentage_discount_of_exactly_100_is_accepted(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.coupons.store'), $this->payload(['discount_value' => '100']))
            ->assertRedirect(route('admin.coupons.index'));

        $this->assertSame(1, Coupon::count());
    }

    public function test_fixed_discount_over_100_is_accepted(): void
    {
        // The 100 cap applies to percentages only — a £150 fixed coupon is legal
        $this->actingAs($this->admin)
            ->post(route('admin.coupons.store'), $this->payload([
                'discount_type'  => 'fixed',
                'discount_value' => '150',
            ]))
            ->assertRedirect(route('admin.coupons.index'));

        $this->assertSame(1, Coupon::count());
    }

    public function test_zero_discount_value_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.coupons.store'), $this->payload(['discount_value' => '0']))
            ->assertSessionHasErrors('discount_value');

        $this->assertSame(0, Coupon::count());
    }

    public function test_fixed_discount_exceeding_column_capacity_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.coupons.store'), $this->payload([
                'discount_type'  => 'fixed',
                'discount_value' => '100000000', // > decimal(10,2) max of 99,999,999.99
            ]))
            ->assertSessionHasErrors('discount_value');

        $this->assertSame(0, Coupon::count());
    }

    // ── Discount calculation caps ────────────────────────────────────────────

    public function test_legacy_percentage_over_100_never_discounts_more_than_subtotal(): void
    {
        // Rows that predate the max:100 rule must not produce a negative total
        $coupon = new Coupon(['discount_type' => 'percentage', 'discount_value' => 150]);

        $this->assertSame(50.0, $coupon->calculateDiscount(50.0));
    }

    public function test_fixed_discount_is_capped_at_subtotal(): void
    {
        $coupon = new Coupon(['discount_type' => 'fixed', 'discount_value' => 80]);

        $this->assertSame(25.0, $coupon->calculateDiscount(25.0));
    }
}
