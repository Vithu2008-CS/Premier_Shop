<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Represents a product category used to group related products.
 *
 * Slugs are auto-generated from the name on creation if not provided,
 * matching the same pattern used by Product.
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'image'];

    /** Auto-generate slug from name when not explicitly provided. */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            $category->slug = $category->slug ?? Str::slug($category->name);
        });
    }

    // ── Relationships ────────────────────────────────────────────────────────

    /** All products belonging to this category. */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
