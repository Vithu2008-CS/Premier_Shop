<?php

echo 'categories: '.App\Models\Category::count().PHP_EOL;

$c = App\Models\Category::withCount('products')->latest()->first();
if ($c) {
    echo 'first: '.$c->name.' | slug: '.$c->slug.' | products: '.$c->products_count.PHP_EOL;
}

echo 'uniqueSlug: '.App\Models\Category::uniqueSlug('Test Category').PHP_EOL;
