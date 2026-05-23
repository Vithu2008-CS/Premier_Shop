<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Manages homepage carousel sliders.
 * Sliders are stored as Promotion records with type = 'slider'.
 * Images can be uploaded as files or supplied as external URLs.
 */
class SliderController extends Controller
{
    /** List all slider-type promotions ordered by display priority. */
    public function index()
    {
        $sliders = Promotion::sliders()->orderBy('order_priority')->get();

        return view('admin.sliders.index', compact('sliders'));
    }

    /** Show the create-slider form. */
    public function create()
    {
        return view('admin.sliders.create');
    }

    /** Validate and persist a new slider. Requires either a file or a URL for the image. */
    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'nullable|string|max:255',
            'subtitle'       => 'nullable|string|max:255',
            'image_file'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image_link'     => 'nullable|url',
            'link_url'       => 'nullable|url',
            'button_text'    => 'nullable|string|max:50',
            'order_priority' => 'integer',
        ]);

        // Resolve image: uploaded file takes priority over external link
        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('sliders', 'public');
        } elseif ($request->image_link) {
            $imagePath = $request->image_link;
        }

        if (! $imagePath) {
            return back()->withInput()->withErrors(['image_file' => 'An image file or link is required.']);
        }

        Promotion::create([
            'title'          => $request->title ?? 'New Slider',
            'subtitle'       => $request->subtitle,
            'image_path'     => $imagePath,
            'link_url'       => $request->link_url,
            'button_text'    => $request->button_text,
            'type'           => 'slider',   // differentiates from banner-type promotions
            'order_priority' => $request->order_priority ?? 0,
            'is_active'      => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider created successfully.');
    }

    /** Show the edit form for a slider. 404s if the promotion is not a slider type. */
    public function edit(Promotion $slider)
    {
        if ($slider->type !== 'slider') {
            abort(404);
        }

        return view('admin.sliders.edit', compact('slider'));
    }

    /**
     * Update a slider's content and optionally replace its image.
     * When a local (non-URL) image is replaced, the old file is deleted from storage.
     */
    public function update(Request $request, Promotion $slider)
    {
        if ($slider->type !== 'slider') {
            abort(404);
        }

        $request->validate([
            'title'          => 'nullable|string|max:255',
            'subtitle'       => 'nullable|string|max:255',
            'image_file'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image_link'     => 'nullable|url',
            'link_url'       => 'nullable|url',
            'button_text'    => 'nullable|string|max:50',
            'order_priority' => 'integer',
        ]);

        $data              = $request->only(['title', 'subtitle', 'link_url', 'button_text', 'order_priority']);
        $data['is_active'] = $request->boolean('is_active');

        // Replace image and clean up the old local file if applicable
        if ($request->hasFile('image_file')) {
            if ($slider->image_path && ! str_contains($slider->image_path, 'http')) {
                Storage::disk('public')->delete($slider->image_path);
            }
            $data['image_path'] = $request->file('image_file')->store('sliders', 'public');
        } elseif ($request->image_link) {
            if ($slider->image_path && ! str_contains($slider->image_path, 'http')) {
                Storage::disk('public')->delete($slider->image_path);
            }
            $data['image_path'] = $request->image_link;
        }

        $slider->update($data);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider updated successfully.');
    }

    /** Delete a slider and its locally stored image file. */
    public function destroy(Promotion $slider)
    {
        if ($slider->type !== 'slider') {
            abort(404);
        }

        // Only delete from disk if it's a locally stored path (not an external URL)
        if ($slider->image_path && ! str_contains($slider->image_path, 'http')) {
            Storage::disk('public')->delete($slider->image_path);
        }

        $slider->delete();

        return redirect()->route('admin.sliders.index')->with('success', 'Slider deleted successfully.');
    }
}
