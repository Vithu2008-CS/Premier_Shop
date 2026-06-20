<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Models\UserItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers the admin sales report (index + PDF print): access control,
 * sold/wishlist aggregates, category filtering, sorting, and inclusion
 * of soft-deleted products that still carry sales history.
 */
class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    private Role $adminRole;

    private Role $customerRole;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'is_staff' => true,
        ]);

        $this->customerRole = Role::create([
            'name' => 'customer',
            'display_name' => 'Customer',
            'is_staff' => false,
        ]);

        $this->admin = User::factory()->create(['role_id' => $this->adminRole->id]);
    }

    /** Create a paid order containing the given product so it gains sales history. */
    private function sell(Product $product, int $quantity): void
    {
        $customer = User::factory()->create(['role_id' => $this->customerRole->id]);

        $order = Order::create([
            'user_id' => $customer->id,
            'order_number' => 'PS-'.uniqid(),
            'subtotal' => $product->price * $quantity,
            'total' => $product->price * $quantity,
            'status' => 'pending',
            'shipping_address' => ['address_line' => '1 Test St', 'city' => 'London', 'phone' => '07000000000'],
            'payment_status' => 'paid',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $product->price,
        ]);
    }

    // ── Access control ────────────────────────────────────────────────────────

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.reports.index'))->assertRedirect(route('login'));
    }

    public function test_customer_cannot_access_reports(): void
    {
        $customer = User::factory()->create(['role_id' => $this->customerRole->id]);

        $this->actingAs($customer)
            ->get(route('admin.reports.index'))
            ->assertForbidden();
    }

    public function test_staff_without_reports_permission_is_blocked(): void
    {
        $staffRole = Role::create(['name' => 'packer', 'display_name' => 'Packer', 'is_staff' => true]);
        $staff = User::factory()->create(['role_id' => $staffRole->id]);

        $this->actingAs($staff)
            ->get(route('admin.reports.index'))
            ->assertForbidden();
    }

    public function test_staff_with_reports_permission_can_view(): void
    {
        $perm = Permission::create(['name' => 'reports.view', 'display_name' => 'View Reports', 'group' => 'Reports']);
        $staffRole = Role::create(['name' => 'analyst', 'display_name' => 'Analyst', 'is_staff' => true]);
        $staffRole->permissions()->attach($perm->id);
        $staff = User::factory()->create(['role_id' => $staffRole->id]);

        $this->actingAs($staff)
            ->get(route('admin.reports.index'))
            ->assertOk();
    }

    // ── Index content ─────────────────────────────────────────────────────────

    public function test_index_shows_products_with_sold_totals(): void
    {
        $product = Product::factory()->create(['name' => 'Bestseller Widget']);
        $this->sell($product, 3);
        $this->sell($product, 4);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.index'));

        $response->assertOk()
            ->assertViewIs('admin.reports.index')
            ->assertSee('Bestseller Widget');

        $row = $response->viewData('products')->firstWhere('id', $product->id);
        $this->assertSame(7, (int) $row->total_sold);
    }

    public function test_index_shows_wishlist_counts(): void
    {
        $product = Product::factory()->create();

        foreach (range(1, 2) as $i) {
            UserItem::create([
                'user_id' => User::factory()->create(['role_id' => $this->customerRole->id])->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'type' => 'wishlist',
            ]);
        }

        // A cart row must NOT count towards the wishlist total
        UserItem::create([
            'user_id' => User::factory()->create(['role_id' => $this->customerRole->id])->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'type' => 'cart',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.index'));

        $row = $response->viewData('products')->firstWhere('id', $product->id);
        $this->assertSame(2, (int) $row->total_wishlist);
    }

    public function test_category_filter_limits_results(): void
    {
        $electronics = Category::factory()->create(['name' => 'Electronics']);
        $food = Category::factory()->create(['name' => 'Food']);

        $tv = Product::factory()->create(['category_id' => $electronics->id]);
        $bread = Product::factory()->create(['category_id' => $food->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.index', ['category' => $electronics->id]));

        $ids = $response->viewData('products')->pluck('id');
        $this->assertTrue($ids->contains($tv->id));
        $this->assertFalse($ids->contains($bread->id));
    }

    public function test_sort_by_price_ascending(): void
    {
        Product::factory()->create(['price' => 99.99]);
        Product::factory()->create(['price' => 1.50]);
        Product::factory()->create(['price' => 45.00]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.index', ['sort_by' => 'price', 'order' => 'asc']));

        $prices = $response->viewData('products')->pluck('price')->map(fn ($p) => (float) $p)->all();
        $sorted = $prices;
        sort($sorted);
        $this->assertSame($sorted, $prices);
    }

    public function test_default_sort_is_most_sold_first(): void
    {
        $slow = Product::factory()->create();
        $hot = Product::factory()->create();
        $this->sell($slow, 1);
        $this->sell($hot, 10);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.index'));

        $ids = $response->viewData('products')->pluck('id')->all();
        $this->assertSame($hot->id, $ids[0]);
    }

    public function test_soft_deleted_product_with_sales_stays_in_report(): void
    {
        $product = Product::factory()->create(['name' => 'Discontinued Gadget']);
        $this->sell($product, 5);
        $product->delete();

        $response = $this->actingAs($this->admin)->get(route('admin.reports.index'));

        $response->assertOk()->assertSee('Discontinued Gadget');

        $row = $response->viewData('products')->firstWhere('id', $product->id);
        $this->assertNotNull($row);
        $this->assertSame(5, (int) $row->total_sold);
    }

    public function test_invalid_sort_params_do_not_crash(): void
    {
        Product::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.reports.index', ['sort_by' => 'bogus', 'order' => 'evil']))
            ->assertOk();
    }

    // ── PDF print ─────────────────────────────────────────────────────────────

    public function test_admin_can_download_pdf_report(): void
    {
        $product = Product::factory()->create();
        $this->sell($product, 2);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.print'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString(
            'sales-report-'.now()->format('Y-m-d').'.pdf',
            $response->headers->get('content-disposition')
        );
    }

    public function test_pdf_respects_category_filter(): void
    {
        $electronics = Category::factory()->create();
        Product::factory()->create(['category_id' => $electronics->id]);

        $this->actingAs($this->admin)
            ->get(route('admin.reports.print', ['category' => $electronics->id]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
