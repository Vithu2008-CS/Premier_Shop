<section>
    <header class="mb-4">
        <h4 class="fw-bold text-danger mb-1">
            {{ __('Delete Account') }}
        </h4>

        <p class="text-muted small">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button type="button" class="btn btn-outline-danger px-4" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        {{ __('Delete Account') }}
    </button>

    {{-- Bootstrap Modal --}}
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="confirmUserDeletionModalLabel">{{ __('Confirm Account Deletion') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body py-4">
                        <p class="text-muted small mb-4">
                            {{ __('Are you sure you want to delete your account? All data will be permanently removed. Please enter your password to confirm.') }}
                        </p>

                        <div class="mb-0">
                            <label for="password" class="form-label small fw-bold">{{ __('Password') }}</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                placeholder="{{ __('Enter your password') }}"
                            />
                            @error('password', 'userDeletion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger px-4">
                            {{ __('Permanently Delete Account') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // Auto-show modal if there's a validation error for user deletion
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->userDeletion->isNotEmpty())
            const modalEl = document.getElementById('confirmUserDeletionModal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        @endif
    });
</script>
@endpush
