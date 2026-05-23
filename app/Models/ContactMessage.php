<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Unified mail record for the admin mail centre.
 *
 * The folder column routes each record to its logical mailbox:
 *   'inbox'  — inbound customer contact form submissions
 *   'sent'   — outbound admin emails + order receipt archives
 *   'draft'  — unsent compose drafts saved by admin
 *   'trash'  — soft-deleted messages (moved, not DB-deleted)
 *
 * Flags:
 *   is_read    — marks inbox messages as seen
 *   is_starred — pinned/important messages across any folder
 *   is_trash   — alternate trash flag (folder='trash' is preferred; this is legacy)
 *   tags       — free-text comma-separated labels for filtering
 */
class ContactMessage extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'message',
        'is_read', 'folder', 'is_starred', 'is_trash', 'tags',
    ];

    protected function casts(): array
    {
        return [
            'is_read'    => 'boolean',
            'is_starred' => 'boolean',
            'is_trash'   => 'boolean',
        ];
    }
}
