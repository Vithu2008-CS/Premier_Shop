<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tracks when the last abandoned-cart reminder was emailed to a user.
 * Compared against the cart's latest activity so a user is re-eligible
 * once they change their cart after a reminder (no reset hook needed).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('cart_reminder_sent_at')->nullable()->after('email_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('cart_reminder_sent_at');
        });
    }
};
