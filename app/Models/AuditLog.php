<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Immutable audit trail entry for an admin-panel action.
 *
 * Rows are created automatically by AuditAdminActions middleware for every
 * state-changing (non-GET) request under /admin. The payload column stores
 * the request input with sensitive fields (passwords, tokens) stripped.
 */
class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'method',
        'url',
        'payload',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
