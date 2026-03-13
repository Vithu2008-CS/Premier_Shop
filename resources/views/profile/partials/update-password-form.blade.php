<section>
    <header class="mb-4">
        <h4 class="fw-bold mb-1">
            {{ __('Update Password') }}
        </h4>

        <p class="text-muted small">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div class="row g-3">
            <div class="col-md-6">
                <label for="update_password_current_password" class="form-label">{{ __('Current Password') }}</label>
                <input id="update_password_current_password" name="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password" />
                @error('current_password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 d-none d-md-block"></div> {{-- Spacer for desktop grid --}}

            <div class="col-md-6">
                <label for="update_password_password" class="form-label">{{ __('New Password') }}</label>
                <input id="update_password_password" name="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password" />
                @error('password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="update_password_password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password" />
                @error('password_confirmation', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mt-4 d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-add-cart px-4">{{ __('Save') }}</button>

            @if (session('status') === 'password-updated')
                <span class="text-success small fw-bold animate__animated animate__fadeOut animate__delay-2s">
                    <i class="bi bi-check-circle me-1"></i>{{ __('Saved.') }}
                </span>
            @endif
        </div>
    </form>
</section>
