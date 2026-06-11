<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Generates a slug that is unique within the model's table by appending a
 * numeric suffix on collision (e.g. beverages, beverages-2, beverages-3).
 *
 * Needed because the slug column is UNIQUE but two different names can slugify
 * to the same value — without this, creating such a record throws a constraint
 * violation (500) instead of resolving to a distinct slug.
 */
trait GeneratesUniqueSlug
{
    public static function uniqueSlug(string $value, $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'item';
        $slug = $base;
        $suffix = 2;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
