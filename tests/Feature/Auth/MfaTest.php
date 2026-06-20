<?php

/**
 * MfaTest — email one-time-password second factor at login.
 *
 * Covers: staff/driver enforcement, customer opt-in, correct/wrong/expired
 * codes, the pending-session guard, and the profile opt-in toggle.
 */

namespace Tests\Feature\Auth;

use App\Mail\LoginOtp;
use App\Models\Role;
use App\Models\User;
use App\Services\MfaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MfaTest extends TestCase
{
    use RefreshDatabase;

    private function staffUser(): User
    {
        $role = Role::create(['name' => 'admin', 'display_name' => 'Administrator', 'is_staff' => true]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    /** Log in via the form and return the 6-digit code captured from the email. */
    private function loginAndCaptureCode(User $user): string
    {
        Mail::fake();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('mfa.challenge'));

        $this->assertGuest();

        $code = null;
        Mail::assertSent(LoginOtp::class, function (LoginOtp $mail) use (&$code, $user) {
            if ($mail->hasTo($user->email)) {
                $code = $mail->otp;

                return true;
            }

            return false;
        });

        return $code;
    }

    public function test_customer_without_mfa_logs_in_directly(): void
    {
        $user = User::factory()->create(); // no role → not enforced, not opted in

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    public function test_staff_login_is_challenged_and_emailed_a_code(): void
    {
        $this->loginAndCaptureCode($this->staffUser());
        // assertions live in the helper: redirect to challenge, still guest, mail sent
    }

    public function test_correct_code_completes_login(): void
    {
        $user = $this->staffUser();
        $code = $this->loginAndCaptureCode($user);

        $this->post('/mfa/challenge', ['otp' => $code])
            ->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_code_keeps_user_pending(): void
    {
        $user = $this->staffUser();
        $this->loginAndCaptureCode($user);

        $this->post('/mfa/challenge', ['otp' => '000000']);

        $this->assertGuest();
    }

    public function test_expired_code_is_rejected(): void
    {
        $user = $this->staffUser();
        $code = $this->loginAndCaptureCode($user);

        $this->travel(MfaService::CODE_TTL_MINUTES + 1)->minutes();

        $this->post('/mfa/challenge', ['otp' => $code]);

        $this->assertGuest();
    }

    public function test_challenge_page_without_pending_session_redirects_to_login(): void
    {
        $this->get('/mfa/challenge')->assertRedirect(route('login'));
    }

    public function test_customer_can_opt_in_and_is_then_challenged(): void
    {
        $user = User::factory()->create(['mfa_enabled' => true]);

        Mail::fake();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('mfa.challenge'));

        $this->assertGuest();
        Mail::assertSent(LoginOtp::class);
    }

    public function test_profile_toggle_enables_and_disables_for_customer(): void
    {
        $role = Role::create(['name' => 'customer', 'display_name' => 'Customer', 'is_staff' => false]);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($user)
            ->put(route('profile.mfa.update'), ['mfa_enabled' => 1])
            ->assertRedirect();
        $this->assertTrue($user->fresh()->mfa_enabled);

        $this->actingAs($user)
            ->put(route('profile.mfa.update'), ['mfa_enabled' => 0])
            ->assertRedirect();
        $this->assertFalse($user->fresh()->mfa_enabled);
    }

    public function test_staff_cannot_disable_role_enforced_mfa(): void
    {
        $user = $this->staffUser();

        $this->actingAs($user)
            ->put(route('profile.mfa.update'), ['mfa_enabled' => 0]);

        // Column is irrelevant for enforced roles — they remain required.
        $this->assertTrue($user->fresh()->requiresMfa());
    }
}
