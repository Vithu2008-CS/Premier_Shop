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

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
