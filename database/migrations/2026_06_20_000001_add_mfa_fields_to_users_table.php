<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Email-OTP multi-factor authentication for login.
 *
 *   mfa_enabled          — customer opt-in flag. Staff/admin/driver roles are
 *                          enforced regardless of this column (see User::requiresMfa).
 *   login_otp            — bcrypt HASH of the most recent 6-digit code (never the
 *                          plaintext), so a leaked DB row cannot be replayed.
 *   login_otp_expires_at — code TTL; a code past this instant is rejected.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('mfa_enabled')->default(false)->after('password');
            $table->string('login_otp')->nullable()->after('mfa_enabled');
            $table->timestamp('login_otp_expires_at')->nullable()->after('login_otp');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mfa_enabled', 'login_otp', 'login_otp_expires_at']);
        });
    }
};
