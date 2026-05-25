<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

/**
 * Manages the single global Settings record.
 * All shop-wide configuration (shipping, loyalty, hours) lives in one row.
 * Freeform fields (shop_hours, shop_notice, loyalty) are stored in the
 * JSON `other_settings` column to avoid constant schema changes.
 */
class SettingController extends Controller
{
    /** Load the settings form, creating an empty model if no row exists yet. */
    public function index()
    {
        $settings = Setting::first() ?? new Setting;

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Persist all shop settings.
     * Scalar columns are updated directly; freeform/nested keys are merged
     * into the `other_settings` JSON column so unrelated keys are preserved.
     */
    public function store(Request $request)
    {
        $request->validate([
            'shop_name'                  => 'nullable|string|max:255',
            'origin_address'             => 'nullable|string|max:255',
            'free_delivery_threshold'    => 'nullable|numeric|min:0',
            'free_delivery_radius_miles' => 'nullable|numeric|min:0',
            'surcharge_per_mile'         => 'nullable|numeric|min:0',
            'flat_rate_fee'              => 'nullable|numeric|min:0',
            'shop_hours'                 => 'nullable|array',
            'shop_notice'                => 'nullable|string',
            'loyalty_enabled'            => 'nullable|boolean',
            'points_per_pound'           => 'nullable|integer|min:1',
            'points_redemption_value'    => 'nullable|numeric|min:0',
        ]);

        // Use existing row or create a fresh one (firstOrNew without save)
        $settings = Setting::first() ?? new Setting;

        // Update flat columns — fall back to current value if not submitted
        $settings->shop_name                  = $request->shop_name                  ?? $settings->shop_name;
        $settings->origin_address             = $request->origin_address             ?? $settings->origin_address;
        $settings->free_delivery_threshold    = $request->free_delivery_threshold    ?? $settings->free_delivery_threshold;
        $settings->free_delivery_radius_miles = $request->free_delivery_radius_miles ?? $settings->free_delivery_radius_miles;
        $settings->surcharge_per_mile         = $request->surcharge_per_mile         ?? $settings->surcharge_per_mile;
        $settings->flat_rate_fee              = $request->flat_rate_fee              ?? $settings->flat_rate_fee;

        // Merge into the JSON column — start from existing data so unrelated keys survive
        $other = $settings->other_settings ?? [];

        if ($request->has('shop_hours')) {
            $other['shop_hours'] = $request->shop_hours;
        }
        if ($request->has('shop_notice')) {
            $other['shop_notice'] = $request->shop_notice;
        }

        // Loyalty programme: presence of checkbox = enabled, absence = disabled
        $other['loyalty_enabled'] = $request->has('loyalty_enabled');

        if ($request->has('points_per_pound')) {
            $other['points_per_pound'] = $request->points_per_pound;
        }
        if ($request->has('points_redemption_value')) {
            $other['points_redemption_value'] = $request->points_redemption_value;
        }

        $settings->other_settings = $other;
        $settings->save();

        return back()->with('success', 'Settings updated successfully.');
    }

    /** Renders the contact & social settings form. */
    public function contactIndex()
    {
        $settings = Setting::first() ?? new Setting;

        return view('admin.settings.contact', compact('settings'));
    }

    /** Updates and persists the contact & social settings in other_settings. */
    public function contactStore(Request $request)
    {
        $request->validate([
            'contact_phone'              => 'required|string|max:50',
            'contact_phone_availability' => 'required|string|max:100',
            'contact_email'              => 'required|email|max:100',
            'contact_email_availability' => 'required|string|max:100',
            'contact_address'            => 'required|string|max:255',
            'contact_hours'              => 'required|string|max:255',
            'social_facebook'            => 'nullable|url|max:255',
            'social_instagram'           => 'nullable|url|max:255',
            'social_twitter'             => 'nullable|url|max:255',
            'social_tiktok'              => 'nullable|url|max:255',
        ]);

        $settings = Setting::first() ?? new Setting;
        $other = $settings->other_settings ?? [];

        $other['contact_phone']              = $request->contact_phone;
        $other['contact_phone_availability'] = $request->contact_phone_availability;
        $other['contact_email']              = $request->contact_email;
        $other['contact_email_availability'] = $request->contact_email_availability;
        $other['contact_address']            = $request->contact_address;
        $other['contact_hours']              = $request->contact_hours;
        
        $other['social_facebook']            = $request->social_facebook;
        $other['social_instagram']           = $request->social_instagram;
        $other['social_twitter']             = $request->social_twitter;
        $other['social_tiktok']              = $request->social_tiktok;

        $settings->other_settings = $other;
        $settings->save();

        return back()->with('success', 'Contact and Social settings updated successfully.');
    }
}
