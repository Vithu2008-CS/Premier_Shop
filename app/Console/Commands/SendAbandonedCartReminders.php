<?php

namespace App\Console\Commands;

use App\Mail\AbandonedCartMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

/**
 * Emails customers who left items in their cart and didn't check out.
 *
 * A user is eligible when their cart's most recent activity is older than the
 * idle threshold (default 1h) but not staler than the max age (default 7d), and
 * they haven't already been reminded since that activity. The reminder timestamp
 * is compared against the cart's latest update, so adding new items after a
 * reminder makes the user eligible again — no separate reset is required.
 *
 * Scheduled hourly in routes/console.php.
 */
class SendAbandonedCartReminders extends Command
{
    protected $signature = 'cart:remind-abandoned {--hours=1 : Idle hours before a reminder} {--max-age-days=7 : Ignore carts older than this}';

    protected $description = 'Email customers who left items in their cart';

    public function handle(): int
    {
        $idleCutoff  = now()->subHours((int) $this->option('hours'));
        $staleCutoff = now()->subDays((int) $this->option('max-age-days'));

        $sent = 0;

        User::query()
            ->whereHas('cartItems')
            ->withMax('cartItems', 'updated_at')
            ->chunkById(200, function ($users) use ($idleCutoff, $staleCutoff, &$sent) {
                foreach ($users as $user) {
                    $latest = $user->cart_items_max_updated_at;
                    if (! $latest) {
                        continue;
                    }
                    $latest = Carbon::parse($latest);

                    // Still active (too recent) or abandoned too long ago (too stale).
                    if ($latest->greaterThan($idleCutoff) || $latest->lessThan($staleCutoff)) {
                        continue;
                    }

                    // Already reminded since the last cart change.
                    if ($user->cart_reminder_sent_at && $user->cart_reminder_sent_at->greaterThanOrEqualTo($latest)) {
                        continue;
                    }

                    $items = $user->cartItems()->with('product')->get()
                        ->filter(fn ($i) => $i->product && $i->product->is_active);

                    if ($items->isEmpty()) {
                        continue;
                    }

                    Mail::to($user->email)->send(new AbandonedCartMail($user, $items));
                    $user->forceFill(['cart_reminder_sent_at' => now()])->save();
                    $sent++;
                }
            });

        $this->info("Abandoned-cart reminders sent: {$sent}");

        return self::SUCCESS;
    }
}
