<section>
    <header class="mb-4">
        <h4 class="fw-bold mb-1">
            {{ __('Profile Information') }}
        </h4>

        <p class="text-muted small">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-4">
        @csrf
        @method('patch')

        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">{{ __('Name') }}</label>
                <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-dark mb-1">
                        {{ __('Your email address is unverified.') }}
                    </p>
                    <button form="send-verification" class="btn btn-link p-0 text-primary text-decoration-none small fw-bold">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 text-success small fw-bold">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                    @endif
                </div>
                @endif
            </div>

            <div class="col-md-6">
                <label for="dob" class="form-label">{{ __('Date of Birth') }}</label>
                <input id="dob" name="dob" type="date" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob', $user->dob ? ($user->dob instanceof \Carbon\Carbon ? $user->dob->format('Y-m-d') : $user->dob) : '') }}" />
                @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                <input id="phone" name="phone" type="text" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" autocomplete="tel" />
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label for="address" class="form-label">{{ __('Address Line') }}</label>
                <input id="address" name="address" type="text" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $user->address) }}" autocomplete="street-address" />
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="city" class="form-label">{{ __('City') }}</label>
                <input id="city" name="city" type="text" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $user->city) }}" autocomplete="address-level2" />
                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mt-4 d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-add-cart px-4">{{ __('Save') }}</button>

            @if (session('status') === 'profile-updated')
                <span class="text-success small fw-bold animate__animated animate__fadeOut animate__delay-2s">
                    <i class="bi bi-check-circle me-1"></i>{{ __('Saved.') }}
                </span>
            @endif
        </div>
    </form>
</section>
