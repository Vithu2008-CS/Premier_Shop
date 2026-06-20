<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Guards the category image_link scheme restriction: only http/https URLs may be
 * stored as a category image source (no javascript:/data: schemes).
 */
class AdminCategoryValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'admin', 'display_name' => 'Administrator', 'is_staff' => true]);
        $this->admin = User::factory()->create(['role_id' => $role->id]);
    }

    public function test_https_image_link_is_accepted(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Beverages',
                'image_link' => 'https://example.com/img.jpg',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Beverages']);
    }

    public function test_javascript_scheme_image_link_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Snacks',
                'image_link' => 'javascript:alert(1)',
            ])
            ->assertSessionHasErrors('image_link');

        $this->assertDatabaseMissing('categories', ['name' => 'Snacks']);
    }
}
