{{--
    profile/partials/two-factor-form.blade.php — Email two-factor opt-in
    =====================================================================
    Customers may switch on an emailed sign-in code as a second factor.
    Staff/admin/driver accounts have it enforced by role (read-only note).
    PUT mfa_enabled → profile.mfa.update → ProfileController::updateMfa()
--}}
<section>
    <header class="mb-4">
        <h4 class="fw-bold mb-1">
            {{ __('Two-Factor Sign-In') }}
        </h4>

        <p class="text-muted small mb-0">
            {{ __('Add a second step at login: we email a 6-digit code to confirm it\'s really you.') }}
        </p>
    </header>

    @if ($user->mfaEnforcedByRole())
        <div class="alert alert-info d-flex align-items-center gap-2 mb-0" role="alert">
            <i class="bi bi-shield-lock-fill"></i>
            <span class="small">{{ __('Two-factor sign-in is required for your account and cannot be turned off.') }}</span>
        </div>
    @else
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                @if ($user->mfa_enabled)
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                        <i class="bi bi-shield-check me-1"></i>{{ __('Enabled') }}
                    </span>
                    <span class="text-muted small">{{ __('A code is emailed each time you sign in.') }}</span>
                @else
                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">
                        <i class="bi bi-shield-slash me-1"></i>{{ __('Disabled') }}
                    </span>
                    <span class="text-muted small">{{ __('Your account is protected by password only.') }}</span>
                @endif
            </div>

            <form method="post" action="{{ route('profile.mfa.update') }}">
                @csrf
                @method('put')
                <input type="hidden" name="mfa_enabled" value="{{ $user->mfa_enabled ? 0 : 1 }}">
                @if ($user->mfa_enabled)
                    <button type="submit" class="btn btn-outline-danger px-4 rounded-pill">{{ __('Turn Off') }}</button>
                @else
                    <button type="submit" class="btn btn-accent px-4 rounded-pill">{{ __('Turn On') }}</button>
                @endif
            </form>
        </div>

        @if (session('status') === 'two-factor-enabled')
            <p class="text-success small fw-bold mt-3 mb-0">
                <i class="bi bi-check-circle me-1"></i>{{ __('Two-factor sign-in is now on.') }}
            </p>
        @elseif (session('status') === 'two-factor-disabled')
            <p class="text-muted small fw-bold mt-3 mb-0">
                <i class="bi bi-info-circle me-1"></i>{{ __('Two-factor sign-in is now off.') }}
            </p>
        @endif
    @endif
</section>
