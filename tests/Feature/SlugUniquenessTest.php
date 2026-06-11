<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The slug column is UNIQUE on both categories and products, but different names
 * can slugify to the same value. These guard that a collision resolves to a
 * suffixed slug instead of throwing a unique-constraint violation.
 */
class SlugUniquenessTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_slug_dedupes_on_collision(): void
    {
        $a = Category::create(['name' => 'Beverages']);
        $b = Category::create(['name' => 'Beverages!']); // also slugifies to "beverages"

        $this->assertSame('beverages', $a->slug);
        $this->assertSame('beverages-2', $b->slug);
    }

    public function test_product_slug_dedupes_on_collision(): void
    {
        $category = Category::factory()->create();

        $a = Product::factory()->create(['name' => 'Cool Item', 'slug' => null, 'category_id' => $category->id]);
        $b = Product::factory()->create(['name' => 'Cool Item', 'slug' => null, 'category_id' => $category->id]);

        $this->assertSame('cool-item', $a->slug);
        $this->assertSame('cool-item-2', $b->slug);
    }
}
