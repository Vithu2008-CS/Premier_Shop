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
            'shop_name' => 'nullable|string|max:255',
            'origin_address' => 'nullable|string|max:255',
            'origin_latitude' => 'nullable|numeric|between:-90,90',
            'origin_longitude' => 'nullable|numeric|between:-180,180',
            // min:0 rejects negative rates (0 = free shipping is legitimate);
            // max matches the decimal(10,2) column capacity
            'free_delivery_threshold' => 'nullable|numeric|min:0|max:99999999.99',
            'free_delivery_radius_miles' => 'nullable|numeric|min:0|max:99999999.99',
            'surcharge_per_mile' => 'nullable|numeric|min:0|max:99999999.99',
            'flat_rate_fee' => 'nullable|numeric|min:0|max:99999999.99',
            // Hours: only real weekday keys, H:i times (the footer Carbon::parse()s
            // these on every page — a malformed value would 500 the storefront)
            'shop_hours' => 'nullable|array:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'shop_hours.*' => 'array',
            'shop_hours.*.open' => 'nullable|date_format:H:i',
            'shop_hours.*.close' => 'nullable|date_format:H:i',
            'shop_hours.*.closed' => 'nullable|boolean',
            'shop_notice' => 'nullable|string|max:1000',
            'loyalty_enabled' => 'nullable|boolean',
            'points_per_pound' => 'nullable|integer|min:1|max:1000',
            // gt:0 — a zero rate would let checkout burn a customer's entire
            // points balance for a £0.00 discount
            'points_redemption_value' => 'nullable|numeric|gt:0|max:1000',
        ]);

        // Use existing row or create a fresh one (firstOrNew without save)
        $settings = Setting::first() ?? new Setting;

        // Update flat columns only when submitted — assigning null explicitly
        // would override the column defaults on a fresh row (shop_name is
        // NOT NULL, so the very first save used to fail outright)
        foreach ([
            'shop_name', 'origin_address', 'free_delivery_threshold',
            'free_delivery_radius_miles', 'surcharge_per_mile', 'flat_rate_fee',
        ] as $column) {
            if ($request->filled($column)) {
                $settings->{$column} = $request->input($column);
            }
        }

        // Merge into the JSON column — start from existing data so unrelated keys survive
        $other = $settings->other_settings ?? [];

        if ($request->has('origin_latitude')) {
            $other['origin_latitude'] = $request->filled('origin_latitude') ? (float) $request->origin_latitude : null;
        }
        if ($request->has('origin_longitude')) {
            $other['origin_longitude'] = $request->filled('origin_longitude') ? (float) $request->origin_longitude : null;
        }

        if ($request->has('shop_hours')) {
            // Normalise to exactly {open, close, closed} per day so stray input
            // keys never land in the blob and `closed` is a real boolean
            $hours = [];
            foreach ((array) $request->input('shop_hours', []) as $day => $dayHours) {
                $hours[$day] = [
                    'open' => $dayHours['open'] ?? null,
                    'close' => $dayHours['close'] ?? null,
                    'closed' => filter_var($dayHours['closed'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ];
            }
            $other['shop_hours'] = $hours;
        }
        if ($request->has('shop_notice')) {
            $other['shop_notice'] = $request->shop_notice;
        }

        // Loyalty programme: only the loyalty form submits this field (hidden 0 +
        // checkbox 1). Guarding on presence stops the shop-hours form — which posts
        // to this same route without the field — from silently disabling loyalty.
        if ($request->has('loyalty_enabled')) {
            $other['loyalty_enabled'] = $request->boolean('loyalty_enabled');
        }

        // Cast before storing so the JSON blob holds real numbers, not "5" strings
        if ($request->filled('points_per_pound')) {
            $other['points_per_pound'] = (int) $request->points_per_pound;
        }
        if ($request->filled('points_redemption_value')) {
            $other['points_redemption_value'] = (float) $request->points_redemption_value;
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
            'contact_phone' => 'required|string|max:50',
            'contact_phone_availability' => 'required|string|max:100',
            'contact_email' => 'required|email|max:100',
            'contact_email_availability' => 'required|string|max:100',
            'contact_address' => 'required|string|max:255',
            'contact_hours' => 'required|string|max:255',
            'social_facebook' => 'nullable|url:http,https|max:255',
            'social_instagram' => 'nullable|url:http,https|max:255',
            'social_twitter' => 'nullable|url:http,https|max:255',
            'social_tiktok' => 'nullable|url:http,https|max:255',
        ]);

        $settings = Setting::first() ?? new Setting;
        $other = $settings->other_settings ?? [];

        $other['contact_phone'] = $request->contact_phone;
        $other['contact_phone_availability'] = $request->contact_phone_availability;
        $other['contact_email'] = $request->contact_email;
        $other['contact_email_availability'] = $request->contact_email_availability;
        $other['contact_address'] = $request->contact_address;
        $other['contact_hours'] = $request->contact_hours;

        $other['social_facebook'] = $request->social_facebook;
        $other['social_instagram'] = $request->social_instagram;
        $other['social_twitter'] = $request->social_twitter;
        $other['social_tiktok'] = $request->social_tiktok;

        $settings->other_settings = $other;
        $settings->save();

        return back()->with('success', 'Contact and Social settings updated successfully.');
    }
}
