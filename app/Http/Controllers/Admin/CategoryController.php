<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * CRUD controller for product categories.
 * Images can be supplied as either a file upload or an external URL.
 */
class CategoryController extends Controller
{
    /** List all categories with a product count, newest first. */
    public function index()
    {
        $categories = Category::withCount('products')->latest()->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    /** Show the create-category form. */
    public function create()
    {
        return view('admin.categories.create');
    }

    /** Validate and persist a new category. */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_link' => 'nullable|url:http,https',
        ]);

        // Auto-generate a unique URL-safe slug from the category name
        $validated['slug'] = Category::uniqueSlug($validated['name']);

        // File upload takes priority over a link; store in /storage/categories as WebP
        if ($request->hasFile('image_file')) {
            $path = \App\Helpers\ImageHelper::storeAsWebp($request->file('image_file'), 'categories');
            $validated['image'] = '/storage/'.$path;
        } elseif ($request->filled('image_link')) {
            $validated['image'] = $request->image_link;
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    /** Show the edit form for an existing category. */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /** Validate and persist changes to an existing category. */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            // Unique rule ignores the current category's own row
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
            'description' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_link' => 'nullable|url:http,https',
        ]);

        $validated['slug'] = Category::uniqueSlug($validated['name'], $category->id);

        if ($request->hasFile('image_file')) {
            $path = \App\Helpers\ImageHelper::storeAsWebp($request->file('image_file'), 'categories');
            $validated['image'] = '/storage/'.$path;
        } elseif ($request->filled('image_link')) {
            $validated['image'] = $request->image_link;
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    /** Delete a category. Associated products will have a null category_id due to the nullable FK. */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
