<?php

/**
 * NewsletterController — Email newsletter subscription capture.
 * store(): validates email, prevents duplicates, returns JSON response.
 * Called via AJAX from the homepage newsletter form (ajax-form handler in app.blade.php).
 */

namespace App\Http\Controllers;

use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:150',
        ]);

        $existing = NewsletterSubscription::where('email', $request->email)->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'This email is already subscribed to our newsletter.',
            ]);
        }

        NewsletterSubscription::create([
            'email' => $request->email,
            'subscribed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => '🎉 You\'re now subscribed! Watch your inbox for exclusive deals.',
        ]);
    }
}
