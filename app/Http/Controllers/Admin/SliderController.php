<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Promotion::sliders()->orderBy('order_priority')->get();
        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image_link' => 'nullable|url',
            'link_url' => 'nullable|url',
            'button_text' => 'nullable|string|max:50',
            'order_priority' => 'integer',
        ]);

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('sliders', 'public');
        } elseif ($request->image_link) {
            $imagePath = $request->image_link;
        }

        if (!$imagePath) {
            return back()->withInput()->withErrors(['image_file' => 'An image file or link is required.']);
        }

        Promotion::create([
            'title' => $request->title ?? 'New Slider',
            'subtitle' => $request->subtitle,
            'image_path' => $imagePath,
            'link_url' => $request->link_url,
            'button_text' => $request->button_text,
            'type' => 'slider',
            'order_priority' => $request->order_priority ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider created successfully.');
    }

    public function edit(Promotion $slider)
    {
        if ($slider->type !== 'slider') abort(404);
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, Promotion $slider)
    {
        if ($slider->type !== 'slider') abort(404);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image_link' => 'nullable|url',
            'link_url' => 'nullable|url',
            'button_text' => 'nullable|string|max:50',
            'order_priority' => 'integer',
        ]);

        $data = $request->only(['title', 'subtitle', 'link_url', 'button_text', 'order_priority']);
        $data['is_active'] = $request->boolean('is_active');
        
        if ($request->hasFile('image_file')) {
            if ($slider->image_path && !str_contains($slider->image_path, 'http')) {
                Storage::disk('public')->delete($slider->image_path);
            }
            $data['image_path'] = $request->file('image_file')->store('sliders', 'public');
        } elseif ($request->image_link) {
            if ($slider->image_path && !str_contains($slider->image_path, 'http')) {
                Storage::disk('public')->delete($slider->image_path);
            }
            $data['image_path'] = $request->image_link;
        }

        $slider->update($data);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider updated successfully.');
    }

    public function destroy(Promotion $slider)
    {
        if ($slider->type !== 'slider') abort(404);
        
        if ($slider->image_path && !str_contains($slider->image_path, 'http')) {
            Storage::disk('public')->delete($slider->image_path);
        }
        $slider->delete();

        return redirect()->route('admin.sliders.index')->with('success', 'Slider deleted successfully.');
    }
}
