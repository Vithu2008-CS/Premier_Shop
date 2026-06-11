{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ route('home') }}</loc><changefreq>daily</changefreq><priority>1.0</priority></url>
    <url><loc>{{ route('products.index') }}</loc><changefreq>daily</changefreq><priority>0.9</priority></url>
    <url><loc>{{ route('offers') }}</loc><changefreq>daily</changefreq><priority>0.8</priority></url>
    <url><loc>{{ route('categories') }}</loc><changefreq>weekly</changefreq><priority>0.7</priority></url>
    <url><loc>{{ route('contact') }}</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>
@foreach($categories as $category)
    <url><loc>{{ route('products.index', ['category' => $category->slug]) }}</loc><changefreq>weekly</changefreq><priority>0.6</priority></url>
@endforeach
@foreach($products as $product)
    <url><loc>{{ route('products.show', $product->slug) }}</loc>@if($product->updated_at)<lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>@endif<changefreq>weekly</changefreq><priority>0.8</priority></url>
@endforeach
</urlset>
