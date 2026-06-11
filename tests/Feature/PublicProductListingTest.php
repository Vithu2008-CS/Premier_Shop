<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers the public product catalogue (products.index) filters that were wired up
 * during the storefront polish: price range, availability, on-offer, broadened
 * search (name + description), minimum approved-review rating, and rating sort.
 *
 * Assertions inspect the paginated `products` view-data (the controller's actual
 * output) rather than rendered HTML, so they validate the query layer directly and
 * aren't affected by markup/escaping. Every request also asserts a 200 so the
 * withAvg aggregate and the correlated rating subquery are proven to execute.
 */
class PublicProductListingTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private User $reviewer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::factory()->create();

        $customerRole = Role::create(['name' => 'customer', 'display_name' => 'Customer', 'is_staff' => false]);
        $this->reviewer = User::factory()->create(['role_id' => $customerRole->id]);
    }

    private function product(array $attrs = []): Product
    {
        return Product::factory()->create(array_merge([
            'category_id' => $this->category->id,
            'is_active'   => true,
        ], $attrs));
    }

    /** Names present in the paginated result for the given query string. */
    private function listedNames(array $query = [])
    {
        $response = $this->get(route('products.index', $query))->assertOk();

        return $response->viewData('products')->getCollection()->pluck('name');
    }

    public function test_listing_renders_and_lists_active_products(): void
    {
        $this->product(['name' => 'AlphaWidget']);
        $this->product(['name' => 'BetaWidget']);

        $names = $this->listedNames();

        $this->assertTrue($names->contains('AlphaWidget'));
        $this->assertTrue($names->contains('BetaWidget'));
    }

    public function test_price_range_filter_narrows_results(): void
    {
        $this->product(['name' => 'CheapThing', 'price' => 10]);
        $this->product(['name' => 'PriceyThing', 'price' => 90]);

        $names = $this->listedNames(['min_price' => 50]);

        $this->assertTrue($names->contains('PriceyThing'));
        $this->assertFalse($names->contains('CheapThing'));
    }

    public function test_in_stock_filter_excludes_sold_out(): void
    {
        $this->product(['name' => 'AvailableThing', 'stock' => 5]);
        $this->product(['name' => 'SoldOutThing', 'stock' => 0]);

        $names = $this->listedNames(['in_stock' => 1]);

        $this->assertTrue($names->contains('AvailableThing'));
        $this->assertFalse($names->contains('SoldOutThing'));
    }

    public function test_on_offer_filter_shows_only_offers(): void
    {
        $this->product(['name' => 'OfferThing', 'retail_offer' => true, 'retail_offer_percentage' => 10]);
        $this->product(['name' => 'RegularThing', 'retail_offer' => false]);

        $names = $this->listedNames(['on_offer' => 1]);

        $this->assertTrue($names->contains('OfferThing'));
        $this->assertFalse($names->contains('RegularThing'));
    }

    public function test_search_matches_description_not_just_name(): void
    {
        $this->product(['name' => 'PlainJar', 'description' => 'Pure organic forest honey']);
        $this->product(['name' => 'OtherJar', 'description' => 'Strawberry jam']);

        $names = $this->listedNames(['search' => 'honey']);

        $this->assertTrue($names->contains('PlainJar'));
        $this->assertFalse($names->contains('OtherJar'));
    }

    public function test_rating_filter_uses_approved_reviews(): void
    {
        $highRated = $this->product(['name' => 'TopRatedThing']);
        $lowRated  = $this->product(['name' => 'PoorlyRatedThing']);

        Review::create([
            'user_id' => $this->reviewer->id, 'product_id' => $highRated->id,
            'rating' => 5, 'is_approved' => true, 'comment' => 'Excellent',
        ]);
        Review::create([
            'user_id' => $this->reviewer->id, 'product_id' => $lowRated->id,
            'rating' => 2, 'is_approved' => true, 'comment' => 'Meh',
        ]);

        $names = $this->listedNames(['rating' => 4]);

        $this->assertTrue($names->contains('TopRatedThing'));
        $this->assertFalse($names->contains('PoorlyRatedThing'));
    }

    public function test_sort_by_rating_does_not_error(): void
    {
        $this->product(['name' => 'SortMeThing']);

        $names = $this->listedNames(['sort' => 'rating']);

        $this->assertTrue($names->contains('SortMeThing'));
    }

    public function test_product_detail_page_renders_with_gallery_polish(): void
    {
        $product = $this->product([
            'name'   => 'DetailWidget',
            'images' => ['/storage/products/x.webp', '/storage/products/y.webp'],
        ]);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('DetailWidget')
            ->assertSee('pdp-gallery-col', false)   // sticky gallery column
            ->assertSee('pdp-zoom', false);         // hover-zoom hook
    }

    public function test_product_detail_shows_frequently_bought_together(): void
    {
        $a = $this->product(['name' => 'AnchorWidget']);
        $b = $this->product(['name' => 'CompanionWidget']);
        $c = $this->product(['name' => 'OtherWidget']);

        // Two orders pair A+B, one pairs A+C → B is the strongest co-purchase for A.
        foreach ([[$a, $b], [$a, $b], [$a, $c]] as $pair) {
            $order = \App\Models\Order::create([
                'user_id' => $this->reviewer->id, 'order_number' => 'PS-'.uniqid(),
                'status' => 'delivered', 'subtotal' => 10, 'total' => 10,
                'shipping_address' => ['address_line' => 'x', 'city' => 'y', 'phone' => 'z'],
                'payment_status' => 'completed', 'payment_method' => 'Bank Transfer',
            ]);
            foreach ($pair as $p) {
                \App\Models\OrderItem::create(['order_id' => $order->id, 'product_id' => $p->id, 'quantity' => 1, 'price' => 5]);
            }
        }

        $this->get(route('products.show', $a->slug))
            ->assertOk()
            ->assertSee('Frequently Bought Together')
            ->assertSee('CompanionWidget');
    }

    public function test_product_detail_includes_product_json_ld(): void
    {
        $product = $this->product(['name' => 'SchemaWidget', 'images' => ['/storage/products/a.webp']]);
        Review::create(['user_id' => $this->reviewer->id, 'product_id' => $product->id, 'rating' => 5, 'is_approved' => true, 'comment' => 'Great']);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('application/ld+json', false)
            ->assertSee('priceCurrency', false)
            ->assertSee('schema.org/InStock', false)
            ->assertSee('AggregateRating', false); // rating present because there is an approved review
    }

    public function test_sitemap_lists_active_products(): void
    {
        $product = $this->product(['name' => 'SitemapWidget']);

        $res = $this->get('/sitemap.xml');

        $res->assertOk()
            ->assertSee('<urlset', false)
            ->assertSee($product->slug, false);
        $this->assertStringContainsString('xml', (string) $res->headers->get('Content-Type'));
    }

    public function test_product_with_no_co_purchases_hides_the_section(): void
    {
        $product = $this->product(['name' => 'LonelyWidget']);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertDontSee('Frequently Bought Together');
    }

    public function test_product_detail_renders_controller_computed_review_aggregates(): void
    {
        // Exercises the with-reviews path: controller computes count/avg/distribution
        // once and the view renders them (no per-call accessor queries).
        $product = $this->product(['name' => 'ReviewedWidget']);

        Review::create(['user_id' => $this->reviewer->id, 'product_id' => $product->id, 'rating' => 4, 'is_approved' => true, 'comment' => 'Good']);
        Review::create(['user_id' => $this->reviewer->id, 'product_id' => $product->id, 'rating' => 5, 'is_approved' => true, 'comment' => 'Great']);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('ReviewedWidget')
            ->assertSee('2 reviews'); // count rendered from the aggregate
    }
}
