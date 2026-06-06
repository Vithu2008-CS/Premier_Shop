<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Manages homepage carousel sliders.
 * Types: slider (main hero) | slider_mid (after New Arrivals) | slider_top (after Recently Viewed)
 * Images can be uploaded as files or supplied as external URLs.
 */
class SliderController extends Controller
{
    private function clearSliderCaches(): void
    {
        Cache::forget('home_sliders_main');
        Cache::forget('home_sliders_mid');
        Cache::forget('home_sliders_top');
    }

    /** List all slider-type promotions grouped by type. */
    public function index()
    {
        $mainSliders = Promotion::where('type', 'slider')->orderBy('order_priority')->get();
        $subSliders1 = Promotion::where('type', 'slider_mid')->orderBy('order_priority')->get();
        $subSliders2 = Promotion::where('type', 'slider_top')->orderBy('order_priority')->get();

        return view('admin.sliders.index', compact('mainSliders', 'subSliders1', 'subSliders2'));
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
            'title'           => 'nullable|string|max:255',
            'image_file'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'image_link'      => 'nullable|url:http,https',
            'link_url'        => 'nullable|url:http,https',
            'button_text'     => 'nullable|string|max:50',
            'button_position' => 'nullable|string|max:30',
            'type'            => 'nullable|in:slider,slider_mid,slider_top',
            'order_priority'  => 'integer',
        ]);

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = \App\Helpers\ImageHelper::storeAsWebp($request->file('image_file'), 'sliders');
        } elseif ($request->image_link) {
            $imagePath = $request->image_link;
        }

        if (! $imagePath) {
            return back()->withInput()->withErrors(['image_file' => 'An image file or link is required.']);
        }

        Promotion::create([
            'title'           => $request->title ?: 'Slider',
            'image_path'      => $imagePath,
            'link_url'        => $request->link_url,
            'button_text'     => $request->button_text,
            'button_position' => $request->button_position ?? 'bottom-center',
            'text_align'      => 'center',
            'type'            => $request->type ?? 'slider',
            'order_priority'  => $request->order_priority ?? 0,
            'is_active'       => $request->boolean('is_active', true),
        ]);

        $this->clearSliderCaches();

        return redirect()->route('admin.sliders.index')->with('success', 'Slider created successfully.');
    }

    /** Show the edit form for a slider. 404s if the promotion is not a slider type. */
    public function edit(Promotion $slider)
    {
        if (! in_array($slider->type, ['slider', 'slider_mid', 'slider_top'])) {
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
        if (! in_array($slider->type, ['slider', 'slider_mid', 'slider_top'])) {
            abort(404);
        }

        $request->validate([
            'title'           => 'nullable|string|max:255',
            'image_file'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'image_link'      => 'nullable|url:http,https',
            'link_url'        => 'nullable|url:http,https',
            'button_text'     => 'nullable|string|max:50',
            'button_position' => 'nullable|string|max:30',
            'type'            => 'nullable|in:slider,slider_mid,slider_top',
            'order_priority'  => 'integer',
        ]);

        $data = $request->only(['title', 'link_url', 'button_text', 'button_position', 'order_priority', 'type']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image_file')) {
            if ($slider->image_path && ! str_contains($slider->image_path, 'http')) {
                Storage::disk('public')->delete($slider->image_path);
            }
            $data['image_path'] = \App\Helpers\ImageHelper::storeAsWebp($request->file('image_file'), 'sliders');
        } elseif ($request->image_link) {
            if ($slider->image_path && ! str_contains($slider->image_path, 'http')) {
                Storage::disk('public')->delete($slider->image_path);
            }
            $data['image_path'] = $request->image_link;
        }

        $slider->update($data);
        $this->clearSliderCaches();

        return redirect()->route('admin.sliders.index')->with('success', 'Slider updated successfully.');
    }

    /** Toggle the active/inactive visibility status of a slider. */
    public function toggleActive(Promotion $slider)
    {
        if (! in_array($slider->type, ['slider', 'slider_mid', 'slider_top'])) {
            abort(404);
        }

        $slider->update(['is_active' => ! $slider->is_active]);
        $this->clearSliderCaches();

        return redirect()->back()->with('success', 'Slider status updated.');
    }

    /** Delete a slider and its locally stored image file. */
    public function destroy(Promotion $slider)
    {
        if (! in_array($slider->type, ['slider', 'slider_mid', 'slider_top'])) {
            abort(404);
        }

        if ($slider->image_path && ! str_contains($slider->image_path, 'http')) {
            Storage::disk('public')->delete($slider->image_path);
        }

        $slider->delete();
        $this->clearSliderCaches();

        return redirect()->route('admin.sliders.index')->with('success', 'Slider deleted.');
    }
}
