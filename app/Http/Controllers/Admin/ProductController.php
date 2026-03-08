<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'product_type' => 'required|in:normal,wholesale',
            'is_age_restricted' => 'boolean',
            'barcode' => 'nullable|string|max:100',
            'product_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'offer_min_qty' => 'nullable|integer|min:1',
            'offer_discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_age_restricted'] = $request->has('is_age_restricted');
        $validated['is_active'] = true;
        $validated['offer_active'] = $request->has('offer_active');

        // Handle images
        $images = [];
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = '/storage/' . $path;
            }
        }
        $validated['images'] = $images;

        $product = Product::create($validated);

        // Generate unique QR Code
        $this->generateQrCode($product);

        return redirect()->route('admin.products.index')->with('success', 'Product created with QR code!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'product_type' => 'required|in:normal,wholesale',
            'is_age_restricted' => 'boolean',
            'barcode' => 'nullable|string|max:100',
            'product_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'offer_min_qty' => 'nullable|integer|min:1',
            'offer_discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $validated['is_age_restricted'] = $request->has('is_age_restricted');
        $validated['offer_active'] = $request->has('offer_active');

        // Handle new images
        if ($request->hasFile('product_images')) {
            $images = $product->images ?? [];
            foreach ($request->file('product_images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = '/storage/' . $path;
            }
            $validated['images'] = $images;
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    public function regenerateQr(Product $product)
    {
        $this->generateQrCode($product);
        return back()->with('success', 'QR code regenerated!');
    }

    // QR Scanner page
    public function scanner()
    {
        return view('admin.products.scanner');
    }

    // API: Find product by QR data
    public function findByQr(Request $request)
    {
        $productId = $request->input('product_id');
        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'stock' => $product->stock,
            'price' => $product->price,
            'image' => $product->first_image,
        ]);
    }

    // API: Update stock via QR scan
    public function updateStock(Request $request, Product $product)
    {
        $request->validate(['stock' => 'required|integer|min:0']);
        $product->update(['stock' => $request->stock]);

        return response()->json(['success' => true, 'new_stock' => $product->stock]);
    }

    /**
     * Generate a unique QR code using the free QR Server API.
     * Each product gets a unique ID embedded in the QR data.
     */
    private function generateQrCode(Product $product): void
    {
        // Unique QR data: product URL + unique hash to ensure uniqueness
        $uniqueHash = Str::uuid()->toString();
        $qrData = url('/admin/products/' . $product->id . '/qr-lookup') . '?uid=' . $uniqueHash;

        // Use QR Server API (free, no extensions needed)
        $apiUrl = 'https://api.qrserver.com/v1/create-qr-code/?' . http_build_query([
            'size' => '300x300',
            'data' => $qrData,
            'color' => '6C5CE7',       // Premier Shop purple
            'bgcolor' => 'FFFFFF',
            'format' => 'png',
            'margin' => 10,
        ]);

        // Download the QR image
        $qrImage = file_get_contents($apiUrl);

        if ($qrImage === false) {
            throw new \Exception('Failed to generate QR code from API.');
        }

        $filename = 'qrcodes/product_' . $product->id . '_' . substr($uniqueHash, 0, 8) . '.png';
        Storage::disk('public')->put($filename, $qrImage);

        $product->update(['qr_code' => '/storage/' . $filename]);
    }
}
