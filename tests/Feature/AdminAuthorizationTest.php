<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Locks in the RBAC enforcement that closes the privilege-escalation hole:
 * before the fix, every /admin route only checked is_staff, so a manager or
 * accountant could open Role management or change a customer's role and promote
 * themselves to admin. Routes now carry per-action permission middleware and the
 * Role/Customer controllers refuse to grant staff/admin access to non-admins.
 */
class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private Role $adminRole;
    private Role $managerRole;
    private Role $accountantRole;
    private Role $customerRole;

    protected function setUp(): void
    {
        parent::setUp();

        $perms = collect([
            'products.view', 'products.create',
            'customers.view', 'customers.update',
            'roles.view', 'roles.create',
        ])->mapWithKeys(fn ($name) => [
            $name => Permission::create(['name' => $name, 'display_name' => $name, 'group' => 'Test']),
        ]);

        $this->adminRole = Role::create(['name' => 'admin', 'display_name' => 'Administrator', 'is_staff' => true]);

        $this->managerRole = Role::create(['name' => 'manager', 'display_name' => 'Manager', 'is_staff' => true]);
        $this->managerRole->permissions()->sync([
            $perms['products.view']->id, $perms['products.create']->id, $perms['customers.view']->id,
        ]);

        $this->accountantRole = Role::create(['name' => 'accountant', 'display_name' => 'Accountant', 'is_staff' => true]);
        $this->accountantRole->permissions()->sync([$perms['products.view']->id, $perms['customers.view']->id]);

        $this->customerRole = Role::create(['name' => 'customer', 'display_name' => 'Customer', 'is_staff' => false]);

        $this->perms = $perms->all();
    }

    private array $perms = [];

    private function user(Role $role): User
    {
        return User::factory()->create(['role_id' => $role->id]);
    }

    // ── Route-level permission enforcement ────────────────────────────────────

    public function test_admin_can_open_role_management(): void
    {
        $this->actingAs($this->user($this->adminRole))
            ->get(route('admin.roles.index'))
            ->assertOk();
    }

    public function test_manager_cannot_open_role_management(): void
    {
        // Manager has no roles.* permission — the escalation entry point is blocked.
        $this->actingAs($this->user($this->managerRole))
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    public function test_accountant_cannot_create_products(): void
    {
        $this->actingAs($this->user($this->accountantRole))
            ->post(route('admin.products.store'), [])
            ->assertForbidden();
    }

    public function test_manager_can_view_products_they_are_granted(): void
    {
        $this->actingAs($this->user($this->managerRole))
            ->get(route('admin.products.index'))
            ->assertOk();
    }

    // ── The headline breach: promoting yourself/others to admin ───────────────

    public function test_manager_cannot_change_a_customer_role(): void
    {
        $manager  = $this->user($this->managerRole);
        $customer = $this->user($this->customerRole);

        // Manager lacks customers.update → blocked at the route before any logic runs.
        $this->actingAs($manager)
            ->put(route('admin.customers.update', $customer), ['role_id' => $this->adminRole->id])
            ->assertForbidden();

        $this->assertSame($this->customerRole->id, $customer->fresh()->role_id);
    }

    public function test_non_admin_staff_with_customers_update_still_cannot_grant_admin(): void
    {
        // A custom staff role that DOES hold customers.update — the route middleware
        // passes, so the controller's defense-in-depth guard must stop the escalation.
        $custom = Role::create(['name' => 'support', 'display_name' => 'Support', 'is_staff' => true]);
        $custom->permissions()->sync([$this->perms['customers.update']->id]);

        $support  = $this->user($custom);
        $customer = $this->user($this->customerRole);

        $this->actingAs($support)
            ->put(route('admin.customers.update', $customer), ['role_id' => $this->adminRole->id])
            ->assertForbidden();

        $this->assertSame($this->customerRole->id, $customer->fresh()->role_id);
    }

    public function test_non_admin_staff_can_still_assign_non_staff_role(): void
    {
        // Positive control: the guard only blocks staff/admin escalation, not normal edits.
        $custom = Role::create(['name' => 'support', 'display_name' => 'Support', 'is_staff' => true]);
        $custom->permissions()->sync([$this->perms['customers.update']->id]);

        $support  = $this->user($custom);
        $customer = $this->user($this->customerRole);
        $other    = Role::create(['name' => 'vip', 'display_name' => 'VIP', 'is_staff' => false]);

        $this->actingAs($support)
            ->put(route('admin.customers.update', $customer), ['role_id' => $other->id])
            ->assertRedirect();

        $this->assertSame($other->id, $customer->fresh()->role_id);
    }

    // ── System-role safeguards ─────────────────────────────────────────────────

    public function test_admin_role_keeps_staff_flag_even_when_unchecked(): void
    {
        // Unchecking is_staff on the admin role would lock every admin out of the
        // panel (AdminMiddleware gates on is_staff) — the controller must force it.
        $this->actingAs($this->user($this->adminRole))
            ->put(route('admin.roles.update', $this->adminRole), [
                'display_name' => 'Administrator',
                // no is_staff key — checkbox unchecked
            ])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertTrue($this->adminRole->fresh()->is_staff);
    }

    public function test_driver_role_cannot_be_deleted(): void
    {
        // Driver role is load-bearing (DriverMiddleware, driver account creation)
        // and must survive deletion even with zero assigned users.
        $driver = Role::create(['name' => 'driver', 'display_name' => 'Driver', 'is_staff' => false]);

        $this->actingAs($this->user($this->adminRole))
            ->delete(route('admin.roles.destroy', $driver))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('roles', ['name' => 'driver']);
    }

    public function test_dead_customer_role_patch_route_is_removed(): void
    {
        // The old PATCH customers/{customer}/role route pointed to a controller
        // method that no longer exists and produced a 500 on dispatch.
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('admin.customers.updateRole'));
    }

    public function test_non_admin_staff_cannot_create_a_staff_role(): void
    {
        $custom = Role::create(['name' => 'support', 'display_name' => 'Support', 'is_staff' => true]);
        $custom->permissions()->sync([$this->perms['roles.create']->id]);

        $this->actingAs($this->user($custom))
            ->post(route('admin.roles.store'), [
                'name' => 'shadow_admin',
                'display_name' => 'Shadow Admin',
                'is_staff' => 1,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('roles', ['name' => 'shadow_admin']);
    }
}
