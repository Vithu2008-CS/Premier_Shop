<?php

namespace Tests\Feature;

use App\Mail\AbandonedCartMail;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Models\UserItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Covers the cart:remind-abandoned command eligibility rules: idle long enough,
 * not too stale, and not already reminded since the cart last changed (which
 * makes a user re-eligible after adding new items).
 */
class AbandonedCartTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'customer', 'display_name' => 'Customer', 'is_staff' => false]);
        $this->user = User::factory()->create(['role_id' => $role->id]);
        $this->product = Product::factory()->create([
            'category_id' => Category::factory()->create()->id,
            'is_active' => true, 'stock' => 10,
        ]);
    }

    /** Add a cart item whose last activity was $hoursAgo hours ago. */
    private function cartItem(float $hoursAgo): void
    {
        $item = UserItem::create([
            'user_id' => $this->user->id, 'product_id' => $this->product->id,
            'quantity' => 1, 'type' => 'cart',
        ]);
        UserItem::where('id', $item->id)->update(['updated_at' => now()->subMinutes((int) round($hoursAgo * 60))]);
    }

    public function test_idle_cart_triggers_a_reminder(): void
    {
        Mail::fake();
        $this->cartItem(2); // idle 2h, threshold 1h

        $this->artisan('cart:remind-abandoned')->assertExitCode(0);

        Mail::assertSent(AbandonedCartMail::class, fn ($m) => $m->hasTo($this->user->email));
        $this->assertNotNull($this->user->fresh()->cart_reminder_sent_at);
    }

    public function test_recently_active_cart_is_not_reminded(): void
    {
        Mail::fake();
        $this->cartItem(0.1); // 6 minutes ago — still active

        $this->artisan('cart:remind-abandoned')->assertExitCode(0);

        Mail::assertNothingSent();
        $this->assertNull($this->user->fresh()->cart_reminder_sent_at);
    }

    public function test_user_already_reminded_is_not_emailed_again(): void
    {
        Mail::fake();
        $this->cartItem(2);
        // Reminder already sent after the cart's last change.
        $this->user->forceFill(['cart_reminder_sent_at' => now()->subHour()])->save();

        $this->artisan('cart:remind-abandoned')->assertExitCode(0);

        Mail::assertNothingSent();
    }

    public function test_new_cart_activity_after_a_reminder_makes_user_eligible_again(): void
    {
        Mail::fake();
        // Reminded 3h ago, but the cart was touched more recently (1.5h ago).
        $this->cartItem(1.5);
        $this->user->forceFill(['cart_reminder_sent_at' => now()->subHours(3)])->save();

        $this->artisan('cart:remind-abandoned')->assertExitCode(0);

        Mail::assertSent(AbandonedCartMail::class);
    }

    public function test_stale_cart_is_ignored(): void
    {
        Mail::fake();
        $this->cartItem(24 * 10); // 10 days ago, beyond the 7-day max age

        $this->artisan('cart:remind-abandoned')->assertExitCode(0);

        Mail::assertNothingSent();
    }
}
