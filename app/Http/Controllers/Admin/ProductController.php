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
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(15);

        return view('admin.products.index', compact('products'));
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
        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'description'             => 'nullable|string',
            'price'                   => 'required|numeric|min:0',
            'wholesale_price'         => 'nullable|numeric|min:0',
            'stock'                   => 'required|integer|min:0',
            'category_id'             => 'nullable|exists:categories,id',
            'product_type'            => 'required|in:normal,wholesale',
            'is_age_restricted'       => 'boolean',
            'barcode'                 => 'nullable|string|max:100',
            'product_images.*'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'offer_min_qty'           => 'nullable|integer|min:1',
            'offer_discount_percent'  => 'nullable|numeric|min:0|max:100',
        ]);

        // Derive URL-friendly slug from the product name
        $validated['slug']               = Str::slug($validated['name']);
        // Checkboxes not submitted = false; use has() instead of boolean() to handle missing key
        $validated['is_age_restricted']  = $request->has('is_age_restricted');
        $validated['is_active']          = true;
        $validated['offer_active']       = $request->has('offer_active');

        // Upload each product image to /storage/products and store its public path
        $images = [];
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $image) {
                $path     = $image->store('products', 'public');
                $images[] = '/storage/'.$path;
            }
        }
        $validated['images'] = $images;

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
        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'description'             => 'nullable|string',
            'price'                   => 'required|numeric|min:0',
            'wholesale_price'         => 'nullable|numeric|min:0',
            'stock'                   => 'required|integer|min:0',
            'category_id'             => 'nullable|exists:categories,id',
            'product_type'            => 'required|in:normal,wholesale',
            'is_age_restricted'       => 'boolean',
            'barcode'                 => 'nullable|string|max:100',
            'product_images.*'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'offer_min_qty'           => 'nullable|integer|min:1',
            'offer_discount_percent'  => 'nullable|numeric|min:0|max:100',
        ]);

        $validated['is_age_restricted'] = $request->has('is_age_restricted');
        $validated['offer_active']      = $request->has('offer_active');

        // Append any new uploads to the product's existing image array
        if ($request->hasFile('product_images')) {
            $images = $product->images ?? [];
            foreach ($request->file('product_images') as $image) {
                $path     = $image->store('products', 'public');
                $images[] = '/storage/'.$path;
            }
            $validated['images'] = $images;
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated!');
    }

    /** Soft-delete a product. */
    public function destroy(Product $product)
    {
        $product->delete();

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
