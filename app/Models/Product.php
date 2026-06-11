<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a product in the shop catalogue.
 *
 * Offer system: a product can have a bulk-buy discount defined by
 * offer_min_qty (minimum quantity to qualify) and offer_discount_percent.
 * The computed offer_price and has_offer accessors are appended to every
 * serialisation so the frontend never needs to recalculate them.
 *
 * Images are stored as a JSON array of public paths; first_image falls
 * back to a placeholder when the array is empty.
 */
class Product extends Model
{
    use GeneratesUniqueSlug, HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'wholesale_price', 'stock',
        'category_id', 'images', 'product_type', 'is_age_restricted',
        'qr_code', 'barcode', 'is_active',
        'offer_min_qty', 'offer_discount_percent', 'offer_active',
        'weight', 'retail_offer', 'retail_offer_percentage',
    ];

    // These virtual attributes are included in toArray() / toJson()
    protected $appends = ['first_image', 'has_offer', 'offer_price', 'active_price'];

    protected function casts(): array
    {
        return [
            'images'                 => 'array',
            'price'                  => 'decimal:2',
            'wholesale_price'        => 'decimal:2',
            'is_age_restricted'      => 'boolean',
            'is_active'              => 'boolean',
            'offer_active'           => 'boolean',
            'offer_discount_percent' => 'decimal:2',
            'weight'                 => 'decimal:2',
            'retail_offer'           => 'boolean',
            'retail_offer_percentage'=> 'decimal:2',
        ];
    }

    /**
     * Auto-generate a slug from the product name on creation if not explicitly provided.
     * Using boot() rather than an observer keeps the logic close to the model.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            $product->slug = $product->slug ?: static::uniqueSlug($product->name);
        });
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /** All OrderItem rows that include this product (used for sales reporting). */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /** UserItem rows where type = 'wishlist' (used for wishlist counts in reports). */
    public function wishlistedBy()
    {
        return $this->hasMany(UserItem::class)->wishlist();
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    /** Average star rating across approved reviews only. Returns 0 when no reviews exist. */
    public function getAverageRatingAttribute(): float
    {
        return (float) ($this->reviews()->approved()->avg('rating') ?? 0);
    }

    /** Get the current active retail price of the product, taking into account any active retail offer. */
    public function getActivePriceAttribute(): float
    {
        if ($this->retail_offer && $this->retail_offer_percentage > 0) {
            return round($this->price * (1 - $this->retail_offer_percentage / 100), 2);
        }
        return (float) $this->price;
    }

    /** Count of approved reviews displayed on the product page. */
    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->approved()->count();
    }

    /**
     * Always normalize the images array to sequential integer keys.
     */
    public function getImagesAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }

        $images = is_array($value) ? $value : json_decode($value, true);

        if (! is_array($images)) {
            return [];
        }

        return array_values(array_filter($images));
    }

    /** Returns the first uploaded image path, or a placeholder fallback. */
    public function getFirstImageAttribute(): string
    {
        $images = $this->images;

        return ! empty($images) ? $images[0] : '/images/placeholder-product.png';
    }

    /** True when all three offer fields are set and offer is currently active. */
    public function getHasOfferAttribute(): bool
    {
        return $this->offer_active
            && $this->offer_min_qty
            && $this->offer_discount_percent;
    }

    /**
     * The discounted unit price when the offer is active.
     * Returns null when no offer applies so blade can test `@if($product->offer_price)`.
     */
    public function getOfferPriceAttribute(): ?float
    {
        if (! $this->has_offer) {
            return null;
        }

        return round($this->price * (1 - $this->offer_discount_percent / 100), 2);
    }

    /** Returns true when stock is greater than zero. */
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /** Filter to products that have a configured active bulk-buy offer. */
    public function scopeWithActiveOffers($query)
    {
        return $query->where('offer_active', true)
            ->whereNotNull('offer_min_qty')
            ->whereNotNull('offer_discount_percent');
    }
}
