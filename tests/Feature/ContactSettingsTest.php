<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'is_staff' => true,
        ]);

        $customerRole = Role::create([
            'name' => 'customer',
            'display_name' => 'Customer',
            'is_staff' => false,
        ]);

        $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
        $this->customer = User::factory()->create(['role_id' => $customerRole->id]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'contact_phone'              => '+44 770 000 0000',
            'contact_phone_availability' => 'Available 24/7',
            'contact_email'              => 'support@shop.test',
            'contact_email_availability' => 'Replies within 24 hours',
            'contact_address'            => 'London, United Kingdom',
            'contact_hours'              => 'Mon-Sat, 9 AM - 6 PM',
            'social_facebook'            => 'https://facebook.com/shop',
            'social_instagram'           => null,
            'social_twitter'             => null,
            'social_tiktok'              => null,
        ], $overrides);
    }

    public function test_admin_can_view_contact_settings_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.contact'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.settings.contact');
    }

    public function test_customer_cannot_view_contact_settings_page()
    {
        $response = $this->actingAs($this->customer)->get(route('admin.settings.contact'));
        $response->assertStatus(403);
    }

    public function test_admin_can_save_contact_settings_on_fresh_install()
    {
        $this->assertDatabaseCount('settings', 0);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.contact.store'), $this->validPayload());

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $settings = Setting::first();
        $this->assertNotNull($settings);
        $this->assertEquals('+44 770 000 0000', $settings->other_settings['contact_phone']);
        $this->assertEquals('https://facebook.com/shop', $settings->other_settings['social_facebook']);
    }

    public function test_saving_contact_settings_preserves_unrelated_other_settings_keys()
    {
        Setting::create([
            'shop_name' => 'Test Shop',
            'other_settings' => ['loyalty_enabled' => true, 'points_per_pound' => 5],
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.settings.contact.store'), $this->validPayload());

        $settings = Setting::first();
        $this->assertTrue($settings->other_settings['loyalty_enabled']);
        $this->assertEquals(5, $settings->other_settings['points_per_pound']);
        $this->assertEquals('London, United Kingdom', $settings->other_settings['contact_address']);
    }

    public function test_invalid_social_url_is_rejected()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.contact.store'), $this->validPayload([
                'social_facebook' => '#',
            ]));

        $response->assertSessionHasErrors('social_facebook');
    }

    public function test_required_contact_fields_are_enforced()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.contact.store'), $this->validPayload([
                'contact_email' => 'not-an-email',
                'contact_phone' => '',
            ]));

        $response->assertSessionHasErrors(['contact_email', 'contact_phone']);
    }
}
