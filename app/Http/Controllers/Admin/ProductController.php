<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Manages the admin product catalogue: CRUD, QR code generation, and the scanner page.
 */
class ProductController extends Controller
{
    /** List all products paginated with their category. */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Product::with('category')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($catQuery) use ($search) {
                      $catQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $products = $query->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products', 'search'));
    }

    /** API — search suggestions for admin product index. */
    public function suggest(Request $request)
    {
        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $products = Product::with('category:id,name')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('barcode', 'like', "%{$q}%")
                      ->orWhereHas('category', function ($catQuery) use ($q) {
                          $catQuery->where('name', 'like', "%{$q}%");
                      });
            })
            ->limit(8)
            ->get(['id', 'name', 'price', 'images', 'category_id', 'stock']);

        return response()->json(
            $products->map(fn ($p) => [
                'id'       => $p->id,
                'name'     => $p->name,
                'price'    => '£'.number_format($p->price, 2),
                'stock'    => $p->stock,
                'image'    => $p->first_image,
                'category' => $p->category?->name,
                'url'      => route('admin.products.edit', $p),
            ])
        );
    }

    /** Show the create-product form with category options. */
    public function create()
    {
        $categories = Category::all();

        return view('admin.products.create', compact('categories'));
    }

    /** Validate, persist, and generate QR code for a new product. */
    public function store(Request $request)
    {
        $rules = [
            'name'                    => 'required|string|max:255',
            'description'             => 'nullable|string',
            'price'                   => 'required|numeric|min:0',
            'stock'                   => 'required|integer|min:0',
            'category_id'             => 'nullable|exists:categories,id',
            'product_type'            => 'required|in:normal,wholesale',
            'is_age_restricted'       => 'boolean',
            'barcode'                 => 'nullable|string|max:100',
            'product_images.*'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'offer_min_qty'           => 'nullable|integer|min:1',
            'offer_discount_percent'  => 'nullable|numeric|min:0|max:100',
            'retail_offer_percentage' => 'required_if:retail_offer,1|nullable|numeric|min:0|max:100',
        ];

        if ($request->has('weight_matters')) {
            $rules['weight'] = 'required|numeric|gt:0';
        } else {
            $rules['weight'] = 'nullable';
        }

        $validated = $request->validate($rules);

        // Derive a unique URL-friendly slug from the product name
        $validated['slug']               = Product::uniqueSlug($validated['name']);
        // Checkboxes not submitted = false; use has() instead of boolean() to handle missing key
        $validated['is_age_restricted']  = $request->has('is_age_restricted');
        $validated['is_active']          = true;
        $validated['offer_active']       = $request->has('offer_active');
        $validated['retail_offer']       = $request->has('retail_offer');
        $validated['retail_offer_percentage'] = $request->has('retail_offer') ? $request->input('retail_offer_percentage', 0.00) : 0.00;
        $validated['weight']             = $request->has('weight_matters') ? $request->input('weight', 0.00) : 0.00;

        // Upload each product image to /storage/products and store its public path as WebP
        $images = [];
        if ($request->filled('images')) {
            $decoded = is_string($request->images) ? json_decode($request->images, true) : $request->images;
            $images = is_array($decoded) ? $decoded : [];
        }
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $image) {
                $path     = \App\Helpers\ImageHelper::storeAsWebp($image, 'products');
                $images[] = '/storage/'.$path;
            }
        }
        $validated['images'] = array_values(array_filter($images));

        $product = Product::create($validated);

        // QR generation calls an external API; failure is non-fatal — just log it
        try {
            $this->generateQrCode($product);
        } catch (\Exception $e) {
            \Log::warning('QR code generation failed for product '.$product->id.': '.$e->getMessage());
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created!');
    }

    /** Show the edit form for an existing product. */
    public function edit(Product $product)
    {
        $categories = Category::all();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /** Validate and persist changes to an existing product. New images are appended, not replaced. */
    public function update(Request $request, Product $product)
    {
        $rules = [
            'name'                    => 'required|string|max:255',
            'description'             => 'nullable|string',
            'price'                   => 'required|numeric|min:0',
            'stock'                   => 'required|integer|min:0',
            'category_id'             => 'nullable|exists:categories,id',
            'product_type'            => 'required|in:normal,wholesale',
            'is_age_restricted'       => 'boolean',
            'barcode'                 => 'nullable|string|max:100',
            'product_images.*'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'offer_min_qty'           => 'nullable|integer|min:1',
            'offer_discount_percent'  => 'nullable|numeric|min:0|max:100',
            'retail_offer_percentage' => 'required_if:retail_offer,1|nullable|numeric|min:0|max:100',
        ];

        if ($request->has('weight_matters')) {
            $rules['weight'] = 'required|numeric|gt:0';
        } else {
            $rules['weight'] = 'nullable';
        }

        $validated = $request->validate($rules);

        $validated['is_age_restricted'] = $request->has('is_age_restricted');
        $validated['offer_active']      = $request->has('offer_active');
        $validated['retail_offer']      = $request->has('retail_offer');
        $validated['retail_offer_percentage'] = $request->has('retail_offer') ? $request->input('retail_offer_percentage', 0.00) : 0.00;
        $validated['weight']            = $request->has('weight_matters') ? $request->input('weight', 0.00) : 0.00;

        // Append any new WebP uploads to the product's existing image array in priority order
        $images = [];
        if ($request->has('images') && $request->filled('images')) {
            $decoded = is_string($request->images) ? json_decode($request->images, true) : $request->images;
            $images = is_array($decoded) ? $decoded : [];
        } else {
            $images = $product->images ?? [];
        }
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $image) {
                $path     = \App\Helpers\ImageHelper::storeAsWebp($image, 'products');
                $images[] = '/storage/'.$path;
            }
        }
        $validated['images'] = array_values(array_filter($images));

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated!');
    }

    /** Upload an image via AJAX and return WebP storage URL. */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $path = \App\Helpers\ImageHelper::storeAsWebp($request->file('file'), 'products');

        return response()->json([
            'url' => '/storage/' . $path
        ]);
    }

    /**
     * Soft-delete a product so past orders keep their line items.
     * Soft deletes bypass the DB cascades, so rows that should not outlive the
     * listing (carts, wishlists, recently-viewed, reviews) are removed explicitly.
     */
    public function destroy(Product $product)
    {
        \DB::transaction(function () use ($product) {
            \App\Models\UserItem::where('product_id', $product->id)->delete();
            \App\Models\RecentlyViewed::where('product_id', $product->id)->delete();
            $product->reviews()->delete();
            $product->delete();
        });

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    /** Force-regenerate the QR code for a product (e.g. after a URL change). */
    public function regenerateQr(Product $product)
    {
        $this->generateQrCode($product);

        return back()->with('success', 'QR code regenerated!');
    }

    /** Render the QR scanner page for stock management. */
    public function scanner()
    {
        return view('admin.products.scanner');
    }

    /** API — look up a product by ID after a QR scan. Returns JSON. */
    public function findByQr(Request $request)
    {
        $productId = $request->input('product_id');
        $product   = Product::find($productId);

        if (! $product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'id'    => $product->id,
            'name'  => $product->name,
            'stock' => $product->stock,
            'price' => $product->price,
            'image' => $product->first_image,
        ]);
    }

    /** API — update stock level for a product via the QR scanner. Returns JSON. */
    public function updateStock(Request $request, Product $product)
    {
        $request->validate(['stock' => 'required|integer|min:0']);
        $product->update(['stock' => $request->stock]);

        return response()->json(['success' => true, 'new_stock' => $product->stock]);
    }

    /**
     * Generate a QR code image via the free QR Server API and store it in /storage/qrcodes.
     * A UUID fragment is appended to the URL data so each code is globally unique even for
     * the same product (prevents QR scanners from hitting a cached response).
     */
    private function generateQrCode(Product $product): void
    {
        // Embed a UUID suffix so re-generated codes can't be confused with old ones
        $uniqueHash = Str::uuid()->toString();
        $qrData     = url('/admin/products/'.$product->id.'/qr-lookup').'?uid='.$uniqueHash;

        // QR Server API — free tier, no API key needed, returns PNG
        $apiUrl = 'https://api.qrserver.com/v1/create-qr-code/?'.http_build_query([
            'size'    => '300x300',
            'data'    => $qrData,
            'color'   => '6C5CE7',   // Premier Shop brand purple
            'bgcolor' => 'FFFFFF',
            'format'  => 'png',
            'margin'  => 10,
        ]);

        $qrImage = file_get_contents($apiUrl);

        if ($qrImage === false) {
            throw new \Exception('Failed to generate QR code from API.');
        }

        // Store with a short UUID prefix so filenames stay unique across regenerations
        $filename = 'qrcodes/product_'.$product->id.'_'.substr($uniqueHash, 0, 8).'.png';
        Storage::disk('public')->put($filename, $qrImage);

        $product->update(['qr_code' => '/storage/'.$filename]);
    }
}
