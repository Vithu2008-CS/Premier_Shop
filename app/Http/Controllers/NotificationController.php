<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;

/**
 * Serves the authenticated user's in-app notifications.
 * Provides a full listing page, a count endpoint for the nav badge,
 * a partial HTML endpoint for the dropdown, and mark-as-read actions.
 */
class NotificationController extends Controller
{
    /** Full paginated notifications page in the user profile area. */
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);

        return view('profile.notifications', compact('notifications'));
    }

    /** JSON endpoint polled by the navbar to keep the unread badge count current. */
    public function count()
    {
        return response()->json([
            'count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Return rendered HTML for the notifications dropdown.
     * Returns the 5 most recent notifications so the dropdown stays lightweight.
     */
    public function latest()
    {
        $notifications = auth()->user()->notifications()->limit(5)->get();

        return view('partials.notifications-dropdown', compact('notifications'))->render();
    }

    /**
     * Mark a single notification as read and redirect to its target URL if set.
     * Ownership check prevents users from marking other users' notifications.
     */
    public function markAsRead(AppNotification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        // Follow the notification's deep-link if one is set (e.g. the order page)
        return $notification->url
            ? redirect($notification->url)
            : back();
    }

    /** Bulk-mark all unread notifications as read for the current user. */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
