<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'is_staff' => true]);
        $customerRole = Role::create(['name' => 'customer', 'display_name' => 'Customer', 'is_staff' => false]);

        $this->admin = User::create([
            'name' => 'Admin Boss',
            'email' => 'admin@shop.com',
            'password' => bcrypt('password123'),
            'dob' => '1990-01-01',
            'phone' => '+447911123456',
            'role_id' => $adminRole->id,
        ]);

        $this->customer = User::create([
            'name' => 'Customer Carl',
            'email' => 'customer@shop.com',
            'password' => bcrypt('password123'),
            'dob' => '1992-02-02',
            'phone' => '+447911654321',
            'role_id' => $customerRole->id,
        ]);
    }

    public function test_admin_post_action_is_audit_logged(): void
    {
        $this->actingAs($this->admin)->post(route('admin.settings.store'), [
            'shop_name' => 'Test Shop',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action'  => 'admin.settings.store',
            'method'  => 'POST',
        ]);
    }

    public function test_get_requests_are_not_audit_logged(): void
    {
        $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_customer_routes_are_not_audit_logged(): void
    {
        $this->actingAs($this->customer)->post(route('newsletter.subscribe'), [
            'email' => 'someone@example.com',
        ]);

        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_sensitive_fields_are_redacted_in_payload(): void
    {
        $this->actingAs($this->admin)->post(route('admin.settings.store'), [
            'shop_name' => 'Test Shop',
            'password'  => 'super-secret',
        ]);

        $log = AuditLog::first();
        $this->assertNotNull($log);
        $this->assertSame('[REDACTED]', $log->payload['password']);
        $this->assertSame('Test Shop', $log->payload['shop_name']);
    }

    public function test_admin_can_view_audit_log_page(): void
    {
        AuditLog::create([
            'user_id' => $this->admin->id,
            'action'  => 'admin.products.update',
            'method'  => 'PUT',
            'url'     => 'http://localhost/admin/products/1',
            'payload' => ['name' => 'Widget'],
            'status'  => 302,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.index'));

        $response->assertOk();
        $response->assertSee('admin.products.update');
    }

    public function test_customer_cannot_view_audit_log_page(): void
    {
        $this->actingAs($this->customer)
            ->get(route('admin.audit-logs.index'))
            ->assertForbidden();
    }
}
