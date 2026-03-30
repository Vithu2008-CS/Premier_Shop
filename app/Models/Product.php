<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'wholesale_price', 'stock',
        'category_id', 'images', 'product_type', 'is_age_restricted',
        'qr_code', 'barcode', 'is_active',
        'offer_min_qty', 'offer_discount_percent', 'offer_active',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'price' => 'decimal:2',
            'wholesale_price' => 'decimal:2',
            'is_age_restricted' => 'boolean',
            'is_active' => 'boolean',
            'offer_active' => 'boolean',
            'offer_discount_percent' => 'decimal:2',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            $product->slug = $product->slug ?? Str::slug($product->name);
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->where('is_approved', true)->count();
    }

    public function wishlistedBy()
    {
        return $this->hasMany(UserItem::class)->wishlist();
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function getFirstImageAttribute(): string
    {
        $images = $this->images;
        return !empty($images) ? $images[0] : '/images/placeholder.png';
    }

    public function getHasOfferAttribute(): bool
    {
        return $this->offer_active && $this->offer_min_qty && $this->offer_discount_percent;
    }

    public function getOfferPriceAttribute(): ?float
    {
        if (!$this->has_offer) return null;
        return round($this->price * (1 - $this->offer_discount_percent / 100), 2);
    }

    public function scopeWithActiveOffers($query)
    {
        return $query->where('offer_active', true)
            ->whereNotNull('offer_min_qty')
            ->whereNotNull('offer_discount_percent');
    }
}
