<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::first() ?? new Setting();
        return view('admin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shop_name' => 'nullable|string|max:255',
            'origin_address' => 'nullable|string|max:255',
            'free_delivery_threshold' => 'nullable|numeric|min:0',
            'free_delivery_radius_miles' => 'nullable|numeric|min:0',
            'surcharge_per_mile' => 'nullable|numeric|min:0',
            'flat_rate_fee' => 'nullable|numeric|min:0',
            'shop_hours' => 'nullable|array',
            'shop_notice' => 'nullable|string',
        ]);

        $settings = Setting::first() ?? new Setting();
        
        $settings->shop_name = $request->shop_name ?? $settings->shop_name;
        $settings->origin_address = $request->origin_address ?? $settings->origin_address;
        $settings->free_delivery_threshold = $request->free_delivery_threshold ?? $settings->free_delivery_threshold;
        $settings->free_delivery_radius_miles = $request->free_delivery_radius_miles ?? $settings->free_delivery_radius_miles;
        $settings->surcharge_per_mile = $request->surcharge_per_mile ?? $settings->surcharge_per_mile;
        $settings->flat_rate_fee = $request->flat_rate_fee ?? $settings->flat_rate_fee;

        $other = $settings->other_settings ?? [];
        if ($request->has('shop_hours')) {
            $other['shop_hours'] = $request->shop_hours;
        }
        if ($request->has('shop_notice')) {
            $other['shop_notice'] = $request->shop_notice;
        }
        $settings->other_settings = $other;
        
        $settings->save();

        return back()->with('success', 'Settings updated successfully.');
    }
}
