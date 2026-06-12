<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TempAdminProductPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'is_staff' => true,
        ]);

        $this->admin = User::factory()->create([
            'role_id' => $role->id,
        ]);
    }

    public function test_index_page_renders()
    {
        Category::factory()->create();
        Product::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertViewIs('admin.products.index');
    }

    public function test_create_page_renders()
    {
        Category::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.products.create'))
            ->assertOk()
            ->assertViewIs('admin.products.create');
    }

    public function test_edit_page_renders()
    {
        $product = Product::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.products.edit', $product))
            ->assertOk()
            ->assertViewIs('admin.products.edit');
    }

    public function test_scanner_page_renders()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.scanner'))
            ->assertOk()
            ->assertViewIs('admin.products.scanner');
    }

    public function test_destroy_soft_deletes_product()
    {
        $product = Product::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_guest_redirected_from_index()
    {
        $this->get(route('admin.products.index'))->assertRedirect();
    }
}
