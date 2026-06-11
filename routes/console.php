<?php

/**
 * Artisan Console Routes
 * ======================
 * Defines custom Artisan commands available via `php artisan <command>`.
 *
 * The 'inspire' command is a Laravel default — prints a motivational quote.
 * Production scheduler tasks (e.g. model:prune for user pruning) are
 * configured in bootstrap/app.php or a dedicated Kernel, not here.
 */

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Email customers who left items in their cart, hourly. withoutOverlapping
// guards against a slow run colliding with the next tick.
Schedule::command('cart:remind-abandoned')->hourly()->withoutOverlapping();
