<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Email address captured via the newsletter signup form on the storefront.
 *
 * subscribed_at records when the signup occurred (cast to datetime).
 * No unsubscribe mechanism is currently implemented — stored for future
 * mailing list / campaign export use.
 */
class NewsletterSubscription extends Model
{
    protected $fillable = ['email', 'subscribed_at'];

    protected function casts(): array
    {
        return [
            'subscribed_at' => 'datetime',
        ];
    }
}
