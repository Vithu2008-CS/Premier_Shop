<?php

namespace App\Services;

use App\Mail\LoginOtp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Email one-time-password second factor for login.
 *
 * Flow: AuthenticatedSessionController validates the password, then — if a
 * second factor is required — calls sendCode() and parks the user id in the
 * session (NOT a full login) until MfaChallengeController confirms the code.
 *
 * The code is stored only as a bcrypt hash with a short TTL, mirroring the
 * registration OTP pattern but persisted on the user row (login is stateless
 * across the two requests, unlike the single-session registration flow).
 */
class MfaService
{
    /** Minutes a freshly issued code stays valid. */
    public const CODE_TTL_MINUTES = 10;

    /**
     * Generate a fresh 6-digit code, persist its hash + expiry, and email the
     * plaintext. Mail failures are logged, never thrown: a flaky SMTP server
     * must not wedge a user who already passed the password check (they can
     * use "resend"). A new code always supersedes any previous one.
     */
    public function sendCode(User $user): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'login_otp' => Hash::make($code),
            'login_otp_expires_at' => now()->addMinutes(self::CODE_TTL_MINUTES),
        ])->save();

        try {
            Mail::to($user->email)->send(new LoginOtp($code, $user->name));
        } catch (\Throwable $e) {
            Log::error('Failed to send login OTP email: '.$e->getMessage());
        }
    }

    /**
     * True when $code matches the stored, unexpired hash. The code is single-use:
     * it is cleared on a successful match so it cannot be replayed.
     */
    public function verify(User $user, string $code): bool
    {
        if (! $user->login_otp || ! $user->login_otp_expires_at) {
            return false;
        }

        if (now()->isAfter($user->login_otp_expires_at)) {
            return false;
        }

        if (! Hash::check($code, $user->login_otp)) {
            return false;
        }

        $this->clear($user);

        return true;
    }

    /** Wipe any outstanding code (after success, or when abandoning a challenge). */
    public function clear(User $user): void
    {
        $user->forceFill([
            'login_otp' => null,
            'login_otp_expires_at' => null,
        ])->save();
    }
}
