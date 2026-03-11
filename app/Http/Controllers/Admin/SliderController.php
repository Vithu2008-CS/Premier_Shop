<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = \App\Models\Slider::orderBy('order')->get();
        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_link' => 'nullable|url',
            'link_url' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:50',
            'order' => 'integer',
        ]);

        $imagePath = '';
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('sliders', 'public');
            $imagePath = '/storage/' . $path;
        } elseif ($request->filled('image_link')) {
            $imagePath = $request->image_link;
        } else {
            return back()->withErrors(['image_file' => 'Please provide an image file or link.'])->withInput();
        }

        \App\Models\Slider::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_path' => $imagePath,
            'link_url' => $request->link_url,
            'button_text' => $request->button_text,
            'is_active' => $request->boolean('is_active'),
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider created successfully.');
    }

    public function edit(\App\Models\Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, \App\Models\Slider $slider)
    {
        $request->validate([
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_link' => 'nullable|url',
            'link_url' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:50',
            'order' => 'integer',
        ]);

        $imagePath = $slider->image_path;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('sliders', 'public');
            $imagePath = '/storage/' . $path;
        } elseif ($request->filled('image_link')) {
            $imagePath = $request->image_link;
        }

        $slider->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_path' => $imagePath,
            'link_url' => $request->link_url,
            'button_text' => $request->button_text,
            'is_active' => $request->boolean('is_active'),
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider updated successfully.');
    }

    public function destroy(\App\Models\Slider $slider)
    {
        $slider->delete();
        return redirect()->route('admin.sliders.index')->with('success', 'Slider deleted successfully.');
    }
}
