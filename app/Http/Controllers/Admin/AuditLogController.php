<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Read-only viewer for the admin action audit trail.
 * Logs are written by AuditAdminActions middleware; there is intentionally
 * no edit or delete endpoint — the trail is immutable from the UI.
 */
class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('method')) {
            $query->where('method', $request->input('method'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('url', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $logs = $query->paginate(25)->withQueryString();

        // Staff users for the actor filter dropdown
        $staffUsers = User::whereHas('role', fn ($q) => $q->where('is_staff', true))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.audit-logs.index', compact('logs', 'staffUsers'));
    }
}
