<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverLocationTrackingTest extends TestCase
{
    use RefreshDatabase;

    private User $driver;
    private User $admin;
    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard roles
        $driverRole = Role::create(['name' => 'driver', 'display_name' => 'Driver', 'is_staff' => false]);
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin', 'is_staff' => true]);
        $customerRole = Role::create(['name' => 'customer', 'display_name' => 'Customer', 'is_staff' => false]);

        // Create users
        $this->driver = User::create([
            'name' => 'Driver Alex',
            'email' => 'driver@shop.com',
            'password' => bcrypt('password123'),
            'dob' => '1995-05-15',
            'phone' => '+447911123456',
            'role_id' => $driverRole->id,
            'is_on_duty' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin Boss',
            'email' => 'admin@shop.com',
            'password' => bcrypt('password123'),
            'dob' => '1988-10-10',
            'phone' => '+447911987654',
            'role_id' => $adminRole->id,
        ]);

        $this->customer = User::create([
            'name' => 'John Customer',
            'email' => 'customer@shop.com',
            'password' => bcrypt('password123'),
            'dob' => '2000-01-01',
            'phone' => '+447911111111',
            'role_id' => $customerRole->id,
        ]);
    }

    /** @test */
    public function driver_can_update_their_location_successfully()
    {
        $response = $this->actingAs($this->driver)
            ->postJson(route('driver.location.update'), [
                'latitude' => 51.5074,
                'longitude' => -0.1278,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'latitude' => 51.5074,
                'longitude' => -0.1278,
            ]);

        $this->driver->refresh();
        $this->assertEquals(51.5074, $this->driver->latitude);
        $this->assertEquals(-0.1278, $this->driver->longitude);
    }

    /** @test */
    public function guest_cannot_update_driver_location()
    {
        $response = $this->postJson(route('driver.location.update'), [
            'latitude' => 51.5074,
            'longitude' => -0.1278,
        ]);

        $response->assertStatus(401); // Unauthorized
    }

    /** @test */
    public function non_driver_user_cannot_update_driver_location()
    {
        $response = $this->actingAs($this->customer)
            ->postJson(route('driver.location.update'), [
                'latitude' => 51.5074,
                'longitude' => -0.1278,
            ]);

        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function location_coordinates_must_be_valid()
    {
        $response = $this->actingAs($this->driver)
            ->postJson(route('driver.location.update'), [
                'latitude' => 120.0, // Invalid latitude (> 90)
                'longitude' => -0.1278,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['latitude']);
    }

    /** @test */
    public function admin_can_retrieve_driver_location_successfully()
    {
        $this->driver->update([
            'latitude' => 51.5074,
            'longitude' => -0.1278,
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson(route('admin.drivers.location', $this->driver));

        $response->assertStatus(200)
            ->assertJson([
                'latitude' => 51.5074,
                'longitude' => -0.1278,
                'is_on_duty' => true,
            ]);
    }

    /** @test */
    public function non_admin_cannot_retrieve_driver_location()
    {
        $response = $this->actingAs($this->customer)
            ->getJson(route('admin.drivers.location', $this->driver));

        $response->assertStatus(403);
    }

    /** @test */
    public function driver_cannot_access_admin_dashboard()
    {
        $response = $this->actingAs($this->driver)
            ->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    /** @test */
    public function driver_cannot_retrieve_other_driver_location_via_admin_route()
    {
        $otherDriver = User::create([
            'name' => 'Driver Bob',
            'email' => 'driver2@shop.com',
            'password' => bcrypt('password123'),
            'dob' => '1995-05-15',
            'phone' => '+447911123457',
            'role_id' => $this->driver->role_id,
            'is_on_duty' => true,
        ]);

        $response = $this->actingAs($this->driver)
            ->getJson(route('admin.drivers.location', $otherDriver));

        $response->assertStatus(403);
    }
}
