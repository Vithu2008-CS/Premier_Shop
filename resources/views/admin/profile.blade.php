{{--
    admin/profile.blade.php — Premium Admin Account Console
    =========================================================
    Elite split-column administrative profile dashboard.
    Left Column: Glassmorphic profile card, glowing avatar wrapper, metadata.
    Right Column: Tabbed controls for Profile Info, Security & Password, and Danger Zone.
--}}
@extends('layouts.admin_noble')
@section('title', 'Admin Profile Console')

@section('content')
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Account Console</li>
    </ol>
</nav>

<div class="row position-relative" style="z-index: 2;">
    {{-- Left Column: Glowing Profile Card --}}
    <div class="col-lg-4 mb-4">
        <div class="card glass-card text-center border-0 shadow-lg h-100 overflow-hidden" style="border-radius: 20px; backdrop-filter: blur(15px);">
            {{-- Decorative header --}}
            <div class="profile-card-header-decor" style="height: 110px; background: linear-gradient(135deg, #727cf5 0%, #6c5ce7 100%); opacity: 0.85;"></div>
            
            <div class="card-body pt-0 pb-4 position-relative">
                {{-- Avatar --}}
                <div class="position-relative d-inline-block" style="margin-top: -65px;">
                    <div class="avatar-halo shadow-lg" style="width: 130px; height: 130px; border-radius: 50%; padding: 4px; background: linear-gradient(135deg, #727cf5, #00cec9); animation: rotateHalo 6s linear infinite;">
                        <div class="w-100 h-100 rounded-circle overflow-hidden bg-dark border border-3 theme-avatar-border">
                            <img id="profileCardAvatar" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="w-100 h-100 object-fit-cover">
                        </div>
                    </div>
                    <label for="profile_photo_file" class="avatar-upload-overlay d-flex align-items-center justify-content-center shadow" style="width: 36px; height: 36px; border-radius: 50%; background: #727cf5; color: #fff; position: absolute; bottom: 5px; right: 5px; cursor: pointer; transition: all 0.2s ease;">
                        <i class="bi bi-camera-fill" style="font-size: 1.1rem;"></i>
                    </label>
                </div>

                {{-- Name & Role --}}
                <h4 class="fw-bold mt-3 mb-1 theme-heading" style="font-family: 'Outfit', sans-serif;">{{ $user->name }}</h4>
                <div class="mb-3 d-flex flex-column align-items-center gap-2">
                    <div>
                        <span class="badge bg-soft-primary px-3 py-1.5 rounded-pill text-uppercase font-weight-bold" style="letter-spacing: 1px; font-size: 0.72rem;">
                            <i class="bi bi-shield-check me-1 text-primary"></i>{{ $user->role->display_name ?? 'Staff Administrator' }}
                        </span>
                    </div>
                    @if($user->isAdmin())
                        <div>
                            <span class="badge bg-soft-success px-2.5 py-1 rounded-pill small text-uppercase font-weight-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                <i class="bi bi-check-circle-fill me-1 text-success"></i>Superuser / Full Access
                            </span>
                        </div>
                    @else
                        <div>
                            <span class="badge bg-soft-info px-2.5 py-1 rounded-pill small text-uppercase font-weight-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                <i class="bi bi-key-fill me-1 text-info"></i>{{ $user->role ? $user->role->permissions()->count() : 0 }} Permissions Active
                            </span>
                        </div>
                    @endif
                </div>

                <hr class="theme-hr" style="margin: 20px 0;">

                {{-- Metadata Grid --}}
                <div class="row text-left g-3 px-2 small text-muted">
                    <div class="col-sm-6 col-12">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-envelope text-primary"></i>
                            <span class="font-weight-medium">Email</span>
                        </div>
                        <div class="theme-value text-truncate" title="{{ $user->email }}">{{ $user->email }}</div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-telephone text-primary"></i>
                            <span class="font-weight-medium">Phone</span>
                        </div>
                        <div class="theme-value">{{ $user->phone ?? 'Not set' }}</div>
                    </div>
                    <div class="col-sm-6 col-12 mt-3">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-calendar-event text-primary"></i>
                            <span class="font-weight-medium">Joined</span>
                        </div>
                        <div class="theme-value">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'Unknown' }}</div>
                    </div>
                    <div class="col-sm-6 col-12 mt-3">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-geo-alt text-primary"></i>
                            <span class="font-weight-medium">City</span>
                        </div>
                        <div class="theme-value">{{ $user->city ?? 'Not set' }}</div>
                    </div>
                </div>

                <hr class="theme-hr" style="margin: 20px 0;">

                {{-- Live Store Metrics --}}
                <div class="text-left px-2 mb-2">
                    <h6 class="theme-heading fw-bold small text-uppercase mb-3" style="letter-spacing: 0.5px;">Live Store Metrics</h6>
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="p-2.5 rounded-3 glass-card border border-light border-opacity-10 d-flex flex-column h-100" style="background: rgba(114, 124, 245, 0.06); transition: all 0.3s ease;">
                                <span class="fw-bold text-primary fs-5" style="font-family: 'Outfit', sans-serif;">{{ \App\Models\Order::count() }}</span>
                                <span class="text-muted small mt-auto" style="font-size: 0.65rem; font-weight: 500; letter-spacing: 0.2px;">Store Orders</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2.5 rounded-3 glass-card border border-light border-opacity-10 d-flex flex-column h-100" style="background: rgba(0, 206, 201, 0.06); transition: all 0.3s ease;">
                                <span class="fw-bold text-info fs-5" style="font-family: 'Outfit', sans-serif;">{{ \App\Models\Product::count() }}</span>
                                <span class="text-muted small mt-auto" style="font-size: 0.65rem; font-weight: 500; letter-spacing: 0.2px;">Products</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2.5 rounded-3 glass-card border border-light border-opacity-10 d-flex flex-column h-100" style="background: rgba(220, 53, 69, 0.06); transition: all 0.3s ease;">
                                <span class="fw-bold text-danger fs-5" style="font-family: 'Outfit', sans-serif;">{{ \App\Models\Review::where('is_approved', false)->count() }}</span>
                                <span class="text-muted small mt-auto" style="font-size: 0.65rem; font-weight: 500; letter-spacing: 0.2px;">Pending Rev.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="theme-hr" style="margin: 20px 0;">

                {{-- Quick shortcuts list --}}
                <div class="text-left px-2">
                    <h6 class="theme-heading fw-bold small text-uppercase mb-3" style="letter-spacing: 0.5px;">Navigation Shortcuts</h6>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm w-100 text-left border-0 rounded-3 py-2 px-3 shortcut-btn d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard Console</span>
                            <i class="bi bi-chevron-right small text-muted"></i>
                        </a>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-sm w-100 text-left border-0 rounded-3 py-2 px-3 shortcut-btn d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-shield-lock me-2 text-primary"></i>Roles & Permissions</span>
                            <i class="bi bi-chevron-right small text-muted"></i>
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-sm w-100 text-left border-0 rounded-3 py-2 px-3 shortcut-btn d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-sliders me-2 text-primary"></i>System Settings</span>
                            <i class="bi bi-chevron-right small text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Tabbed Control Console --}}
    <div class="col-lg-8 mb-4">
        <div class="card glass-card border-0 shadow-lg h-100" style="border-radius: 20px; backdrop-filter: blur(15px);">
            <div class="card-header bg-transparent border-bottom border-light p-0">
                {{-- Custom tab triggers --}}
                <ul class="nav nav-tabs card-header-tabs border-0 m-0 stagger-children" id="profileTab" role="tablist" style="padding: 0 15px;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold py-3 border-0 active-accent" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                            <i class="bi bi-person-gear me-2"></i>Profile Details
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-3 border-0 active-accent" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">
                            <i class="bi bi-lock-fill me-2"></i>Security Control
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-3 border-0 active-accent text-hover-danger" id="danger-tab" data-bs-toggle="tab" data-bs-target="#danger" type="button" role="tab" aria-controls="danger" aria-selected="false">
                            <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>Danger Zone
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4 p-md-5">
                <div class="tab-content" id="profileTabContent">
                    
                    {{-- Tab 1: Profile Details Form --}}
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <h5 class="fw-bold theme-heading mb-1" style="font-family: 'Outfit', sans-serif;">Profile Information</h5>
                        <p class="text-muted small mb-4">Update your administrator profile details, address records, and identity email address.</p>
                        
                        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('patch')

                            {{-- Hidden file input --}}
                            <input type="file" name="profile_photo" id="profile_photo_file" class="d-none" accept="image/*" onchange="previewAvatarImage(this)">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label font-weight-bold theme-label">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                                        <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                                        @error('name') <div class="invalid-feedback font-weight-bold mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label font-weight-bold theme-label">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-envelope text-muted"></i></span>
                                        <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                                        @error('email') <div class="invalid-feedback font-weight-bold mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="dob" class="form-label font-weight-bold theme-label">Date of Birth</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-calendar-event text-muted"></i></span>
                                        <input id="dob" name="dob" type="date" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob', $user->dob ? ($user->dob instanceof \Carbon\Carbon ? $user->dob->format('Y-m-d') : $user->dob) : '') }}" />
                                        @error('dob') <div class="invalid-feedback font-weight-bold mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label font-weight-bold theme-label">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-telephone text-muted"></i></span>
                                        <input id="phone" name="phone" type="text" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" autocomplete="tel" placeholder="E.g., +44 7700 900077" />
                                        @error('phone') <div class="invalid-feedback font-weight-bold mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label font-weight-bold theme-label">Street Address Line</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-geo-alt text-muted"></i></span>
                                        <input id="address" name="address" type="text" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $user->address) }}" autocomplete="street-address" placeholder="Full street address line" />
                                        @error('address') <div class="invalid-feedback font-weight-bold mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="city" class="form-label font-weight-bold theme-label">City</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-building text-muted"></i></span>
                                        <input id="city" name="city" type="text" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $user->city) }}" autocomplete="address-level2" placeholder="E.g., London" />
                                        @error('city') <div class="invalid-feedback font-weight-bold mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-2 d-flex align-items-center gap-3">
                                <button type="submit" class="btn btn-primary px-4 rounded-pill font-weight-bold">
                                    <i class="bi bi-save me-2"></i>Save Details
                                </button>

                                @if (session('status') === 'profile-updated')
                                    <span class="text-success small fw-bold" id="profileSaveNotification">
                                        <i class="bi bi-check-circle-fill me-1"></i>Saved successfully!
                                    </span>
                                @endif
                            </div>
                        </form>
                    </div>

                    {{-- Tab 2: Security & Password Update --}}
                    <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                        <h5 class="fw-bold theme-heading mb-1" style="font-family: 'Outfit', sans-serif;">Security Control</h5>
                        <p class="text-muted small mb-4">Ensure your account is protected with a cryptographically secure, random password.</p>
                        
                        <form method="post" action="{{ route('password.update') }}">
                            @csrf
                            @method('put')

                            <div class="row g-3">
                                <div class="col-md-12 mb-2">
                                    <label for="update_password_current_password" class="form-label font-weight-bold theme-label">Current Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-shield-lock text-muted"></i></span>
                                        <input id="update_password_current_password" name="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password" placeholder="Enter active password" />
                                        @error('current_password', 'updatePassword') <div class="invalid-feedback font-weight-bold mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="update_password_password" class="form-label font-weight-bold theme-label">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-key text-muted"></i></span>
                                        <input id="update_password_password" name="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password" placeholder="Min 12 characters" />
                                        @error('password', 'updatePassword') <div class="invalid-feedback font-weight-bold mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="update_password_password_confirmation" class="form-label font-weight-bold theme-label">Confirm New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-check2 text-muted"></i></span>
                                        <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password" placeholder="Repeat new password" />
                                        @error('password_confirmation', 'updatePassword') <div class="invalid-feedback font-weight-bold mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-2 d-flex align-items-center gap-3">
                                <button type="submit" class="btn btn-primary px-4 rounded-pill font-weight-bold">
                                    <i class="bi bi-key-fill me-2"></i>Update Password
                                </button>

                                @if (session('status') === 'password-updated')
                                    <span class="text-success small fw-bold" id="passwordSaveNotification">
                                        <i class="bi bi-check-circle-fill me-1"></i>Password updated!
                                    </span>
                                @endif
                            </div>
                        </form>
                    </div>

                    {{-- Tab 3: Danger Zone --}}
                    <div class="tab-pane fade" id="danger" role="tabpanel" aria-labelledby="danger-tab">
                        <h5 class="fw-bold text-danger mb-1" style="font-family: 'Outfit', sans-serif;">Danger Zone</h5>
                        <p class="text-muted small mb-4">Permanently terminate your administrative account. This action is final and cascading resources will be pruned.</p>
                        
                        <div class="p-4 border border-danger rounded-3" style="background: rgba(220, 53, 69, 0.03);">
                            <h6 class="fw-bold theme-heading mb-2">Account Deletion Protection</h6>
                            <p class="small theme-value mb-3">
                                Once deleted, your administrative role, permission grids, notifications logs, and associated records will be purged. Please proceed with extreme caution.
                            </p>
                            
                            <button type="button" class="btn btn-danger font-weight-bold px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#confirmAdminDeletionModal">
                                <i class="bi bi-trash3-fill me-2"></i>Delete Account...
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bootstrap Deletion Confirmation Modal --}}
<div class="modal fade" id="confirmAdminDeletionModal" tabindex="-1" aria-labelledby="confirmAdminDeletionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg theme-modal" style="border-radius: 16px; overflow: hidden; border: 1px solid rgba(220, 53, 69, 0.2) !important;">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fw-bold theme-heading" id="confirmAdminDeletionModalLabel">Confirm Account Deletion</h5>
                    <button type="button" class="btn-close theme-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-3 px-4">
                    <p class="small mb-4">
                        Are you sure you want to permanently delete your administrator account? Enter your current password to authorize this action.
                    </p>

                    <div class="mb-2">
                        <label for="password_confirm" class="form-label small fw-bold theme-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-key text-muted"></i></span>
                            <input
                                id="password_confirm"
                                name="password"
                                type="password"
                                class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                placeholder="Enter your active password"
                                required
                            />
                            @error('password', 'userDeletion') <div class="invalid-feedback font-weight-bold mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pb-4 px-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill font-weight-bold px-3 py-1.5" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill font-weight-bold px-3 py-1.5">
                        Confirm Permanent Deletion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Responsive Glass Cards */
    html[data-admin-theme="dark"] .glass-card {
        background: rgba(12, 20, 39, 0.65) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        color: #ffffff !important;
    }
    html[data-admin-theme="light"] .glass-card {
        background: rgba(255, 255, 255, 0.85) !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        color: #1f2937 !important;
    }

    /* Headings */
    html[data-admin-theme="dark"] .theme-heading {
        color: #ffffff !important;
    }
    html[data-admin-theme="light"] .theme-heading {
        color: #111827 !important;
    }

    /* Labels */
    html[data-admin-theme="dark"] .theme-label {
        color: rgba(255, 255, 255, 0.9) !important;
    }
    html[data-admin-theme="light"] .theme-label {
        color: #374151 !important;
    }

    /* Values */
    html[data-admin-theme="dark"] .theme-value {
        color: rgba(255, 255, 255, 0.65) !important;
    }
    html[data-admin-theme="light"] .theme-value {
        color: #4b5563 !important;
    }

    /* Modals */
    html[data-admin-theme="dark"] .theme-modal {
        background: #0c1427 !important;
        color: rgba(255, 255, 255, 0.85) !important;
    }
    html[data-admin-theme="light"] .theme-modal {
        background: #ffffff !important;
        color: #1f2937 !important;
    }

    /* Modal Close Button */
    html[data-admin-theme="dark"] .theme-close-btn {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
    html[data-admin-theme="light"] .theme-close-btn {
        filter: none;
    }

    /* Custom theme responsive divider rules */
    html[data-admin-theme="dark"] .theme-hr {
        border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    html[data-admin-theme="light"] .theme-hr {
        border-top: 1px solid rgba(0, 0, 0, 0.08) !important;
    }

    /* Theme responsive avatar borders */
    html[data-admin-theme="dark"] .theme-avatar-border {
        border-color: #0c1427 !important;
    }
    html[data-admin-theme="light"] .theme-avatar-border {
        border-color: #ffffff !important;
    }

    /* Custom shortcut buttons responsive styling */
    .shortcut-btn {
        background: rgba(114, 124, 245, 0.04) !important;
        transition: all 0.3s ease;
    }
    html[data-admin-theme="dark"] .shortcut-btn {
        color: rgba(255, 255, 255, 0.7) !important;
    }
    html[data-admin-theme="dark"] .shortcut-btn:hover {
        background: rgba(114, 124, 245, 0.12) !important;
        color: #ffffff !important;
    }
    html[data-admin-theme="light"] .shortcut-btn {
        color: #4b5563 !important;
    }
    html[data-admin-theme="light"] .shortcut-btn:hover {
        background: rgba(114, 124, 245, 0.08) !important;
        color: #727cf5 !important;
    }

    /* Premium style variables & transitions specifically for admin profile tab console */
    html[data-admin-theme="dark"] #profileTab .nav-link {
        color: rgba(255, 255, 255, 0.6) !important;
    }
    html[data-admin-theme="light"] #profileTab .nav-link {
        color: #4b5563 !important;
    }
    #profileTab .nav-link {
        background: transparent !important;
        position: relative;
        transition: all 0.3s ease;
    }
    html[data-admin-theme="dark"] #profileTab .nav-link:hover {
        color: #ffffff !important;
    }
    html[data-admin-theme="light"] #profileTab .nav-link:hover {
        color: #111827 !important;
    }
    #profileTab .nav-link.active {
        color: #727cf5 !important;
        background: transparent !important;
    }
    #profileTab .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #727cf5, #00cec9);
        border-radius: 3px 3px 0 0;
        animation: scaleIn 0.25s ease forwards;
    }
    
    .text-hover-danger:hover {
        color: #dc3545 !important;
    }
    .nav-link.active.text-hover-danger {
        color: #dc3545 !important;
    }
    .nav-link.active.text-hover-danger::after {
        background: #dc3545 !important;
    }
    
    .avatar-upload-overlay {
        opacity: 0.9;
        transition: transform 0.2s ease, background-color 0.2s ease;
    }
    .avatar-upload-overlay:hover {
        transform: scale(1.1);
        background-color: #6c5ce7 !important;
    }
    
    @keyframes rotateHalo {
        0% { filter: hue-rotate(0deg); }
        100% { filter: hue-rotate(360deg); }
    }
    
    @keyframes scaleIn {
        from { transform: scaleX(0); }
        to { transform: scaleX(1); }
    }

    /* Mobile Responsive Optimizations */
    @media (max-width: 991px) {
        .col-lg-4, .col-lg-8 {
            margin-bottom: 24px !important;
        }
    }
    
    @media (max-width: 576px) {
        #profileTab {
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            overflow-y: hidden !important;
            -webkit-overflow-scrolling: touch !important;
            padding: 0 5px !important;
        }
        #profileTab::-webkit-scrollbar {
            display: none;
        }
        #profileTab .nav-item {
            white-space: nowrap !important;
        }
        #profileTab .nav-link {
            padding: 12px 14px !important;
            font-size: 0.85rem !important;
        }
        .card-body {
            padding: 24px 16px !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function previewAvatarImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Update card avatar
                const cardAvatar = document.getElementById('profileCardAvatar');
                if (cardAvatar) cardAvatar.src = e.target.result;
                
                // Optional: Update top navbar avatar if present
                const navbarAvatars = document.querySelectorAll('.nav-profile img');
                navbarAvatars.forEach(img => img.src = e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Auto-show modal if there are validation errors for account deletion
        @if($errors->userDeletion->isNotEmpty())
            const modalEl = document.getElementById('confirmAdminDeletionModal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
                // Select danger tab automatically
                const dangerTab = document.getElementById('danger-tab');
                if (dangerTab) {
                    const trigger = new bootstrap.Tab(dangerTab);
                    trigger.show();
                }
            }
        @endif

        // Auto-select security tab if there are validation errors for password updates
        @if($errors->updatePassword->isNotEmpty())
            const securityTab = document.getElementById('security-tab');
            if (securityTab) {
                const trigger = new bootstrap.Tab(securityTab);
                trigger.show();
            }
        @endif

        // Auto-fade notifications after 3 seconds
        const profileNotify = document.getElementById('profileSaveNotification');
        if (profileNotify) {
            setTimeout(() => {
                profileNotify.style.transition = 'opacity 0.8s ease';
                profileNotify.style.opacity = '0';
            }, 3000);
        }
        
        const pwdNotify = document.getElementById('passwordSaveNotification');
        if (pwdNotify) {
            setTimeout(() => {
                pwdNotify.style.transition = 'opacity 0.8s ease';
                pwdNotify.style.opacity = '0';
            }, 3000);
        }
    });
</script>
@endpush
@endsection
