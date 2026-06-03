{{--
    admin/drivers/create.blade.php — Create driver account form
    ============================================================
    Fields: name, email, phone, password. Creates User with 'driver' role.
    POST → admin.drivers.store → DriverController::store()
--}}
@extends('layouts.admin_noble')
@section('title', 'Add New Driver')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap');

.container-fluid { font-family: 'Inter', sans-serif; }

.rounded-3 { border-radius: 12px !important; }
.rounded-4 { border-radius: 18px !important; }
.gap-4     { gap: 24px !important; }

/* Themed cards */
html[data-admin-theme="light"] .theme-card-bg { background-color: #ffffff !important; }
html[data-admin-theme="dark"]  .theme-card-bg { background-color: #0c1427 !important; border: 1px solid rgba(255,255,255,0.05) !important; }
html[data-admin-theme="light"] .text-theme-dark-bold { color: #1e293b !important; }
html[data-admin-theme="dark"]  .text-theme-dark-bold { color: #f1f5f9 !important; }

/* Soft colour tokens */
.bg-soft-primary   { background: rgba(108,92,231,0.1) !important;  color: #6c5ce7 !important; }
.bg-soft-success   { background: rgba(16,185,129,0.1) !important;   color: #10b981 !important; }
.bg-soft-secondary { background: rgba(100,116,139,0.1) !important;  color: #64748b !important; }
.bg-soft-warning   { background: rgba(245,158,11,0.1) !important;   color: #f59e0b !important; }
.bg-soft-danger    { background: rgba(239,68,68,0.1) !important;    color: #ef4444 !important; }

/* Form inputs */
.form-control, select.form-control {
    border-radius: 12px !important;
    border: 1.5px solid rgba(0,0,0,0.07) !important;
    padding: 0.5rem 0.95rem !important;
    font-size: 0.84rem !important;
    transition: all 0.25s ease !important;
    background-color: #ffffff !important;
    color: #1e293b !important;
}
.form-control:focus, select.form-control:focus {
    border-color: #6c5ce7 !important;
    box-shadow: 0 0 0 3.5px rgba(108,92,231,0.15) !important;
}
html[data-admin-theme="dark"] .form-control {
    background-color: #080f1d !important;
    border-color: rgba(255,255,255,0.08) !important;
    color: #e2e8f0 !important;
}
html[data-admin-theme="dark"] .form-control:focus {
    border-color: #a78bfa !important;
    box-shadow: 0 0 0 3.5px rgba(167,139,250,0.2) !important;
}

/* Border */
.border-bottom-subtle { border-bottom: 1.5px solid rgba(108,92,231,0.06) !important; }
html[data-admin-theme="dark"] .border-bottom-subtle { border-bottom: 1.5px solid rgba(255,255,255,0.05) !important; }

/* Pulsing green dot */
.pulse-green {
    width: 8px; height: 8px; background: #10b981; border-radius: 50%;
    display: inline-block; animation: blinkDot 1.5s infinite ease-in-out;
}
@keyframes blinkDot {
    0%,100% { opacity: 0.3; transform: scale(0.9); }
    50%      { opacity: 1;   transform: scale(1.15); }
}

/* Checklist styles */
.checklist-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 16px;
}
.checklist-icon {
    min-width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    flex-shrink: 0;
}

/* Floating save bar */
.floating-save-bar {
    position: fixed; bottom: 24px; left: calc(50% + 120px);
    transform: translateX(-50%); z-index: 1000;
    width: calc(100% - 32px - 240px); max-width: 920px;
    background: rgba(255,255,255,0.85) !important;
    backdrop-filter: blur(12px) !important; -webkit-backdrop-filter: blur(12px) !important;
    border: 1px solid rgba(0,0,0,0.08) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06) !important;
    border-radius: 50px !important;
    transition: all 0.3s ease;
}
html[data-admin-theme="dark"] .floating-save-bar {
    background: rgba(15,23,42,0.85) !important;
    border-color: rgba(255,255,255,0.08) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3) !important;
}
.floating-save-bar .button-group { display: flex; align-items: center; gap: 12px; }
.floating-save-bar .btn {
    display: inline-flex; align-items: center; justify-content: center;
    height: 38px !important; min-width: 100px !important;
    padding: 0 22px !important; font-size: 0.82rem !important; font-weight: 700 !important;
    border-radius: 30px !important; transition: all 0.2s ease !important;
}
.floating-save-bar .btn-outline-light {
    border: 1.5px solid rgba(0,0,0,0.15) !important; background: transparent !important; color: #475569 !important;
}
.floating-save-bar .btn-outline-light:hover { background: rgba(0,0,0,0.04) !important; color: #1e293b !important; }
html[data-admin-theme="dark"] .floating-save-bar .btn-outline-light { border-color: rgba(255,255,255,0.3) !important; color: #fff !important; }
html[data-admin-theme="dark"] .floating-save-bar .btn-outline-light:hover { background: rgba(255,255,255,0.1) !important; }
.floating-save-bar .btn-primary {
    background: linear-gradient(135deg,#6c5ce7,#a78bfa) !important;
    border: none !important; color: #fff !important;
    box-shadow: 0 4px 12px rgba(108,92,231,0.2) !important;
}
.floating-save-bar .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(108,92,231,0.3) !important; color:#fff !important; }
.floating-save-bar .floating-bar-title { color: #0f172a !important; }
html[data-admin-theme="dark"] .floating-save-bar .floating-bar-title { color: #ffffff !important; }

@media (max-width: 991px) { .floating-save-bar { left: 50% !important; width: calc(100% - 32px) !important; } }
@media (max-width: 575px) {
    .floating-save-bar {
        border-radius: 20px !important; padding: 12px 16px !important;
        bottom: 16px !important; flex-direction: column; gap: 10px;
        align-items: stretch !important; text-align: center;
        width: calc(100% - 24px) !important;
    }
    .floating-save-bar .button-group { width: 100%; gap: 8px; }
    .floating-save-bar .btn { min-width: 0 !important; padding: 0 8px !important; font-size: 0.76rem !important; flex: 1; }
    .card-body.p-4.p-md-5 { padding: 1.25rem !important; }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0" style="padding-bottom: 120px;">

    {{-- Breadcrumb --}}
    <nav class="page-breadcrumb d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.drivers.index') }}">Drivers</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add New</li>
        </ol>
        <a href="{{ route('admin.drivers.index') }}"
           class="btn btn-outline-primary btn-sm rounded-pill px-3 d-inline-flex align-items-center"
           style="border-radius:30px !important;font-weight:700;font-family:'Outfit';border:1.5px solid #6c5ce7;color:#6c5ce7;">
            <i class="bi bi-arrow-left mr-2" style="font-size:0.85rem;margin-right:6px;"></i> Back to fleet
        </a>
    </nav>

    <div class="row">
        {{-- Left: Registration Form --}}
        <div class="col-lg-7 mb-4">
            <form action="{{ route('admin.drivers.store') }}" method="POST" id="driver-create-form" class="d-flex flex-column gap-4 w-100">
                @csrf

                {{-- Driver Info --}}
                <div class="card border-0 shadow-sm theme-card-bg" style="border-radius:18px !important;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
                            <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family:'Outfit',sans-serif;">
                                <i class="bi bi-person-badge text-primary mr-2" style="font-size:1.25rem;margin-right:8px;"></i>
                                Driver Information
                            </h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" required placeholder="John Doe">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" required placeholder="driver@example.com">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone') }}" required placeholder="+44 123 456789">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror"
                                       value="{{ old('dob') }}" required>
                                @error('dob')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label small text-muted font-weight-bold">Home Address</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3"
                                      placeholder="Enter full address...">{{ old('address') }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Security Info --}}
                <div class="card border-0 shadow-sm theme-card-bg" style="border-radius:18px !important;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
                            <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family:'Outfit',sans-serif;">
                                <i class="bi bi-shield-lock text-warning mr-2" style="font-size:1.25rem;margin-right:8px;"></i>
                                Security Credentials
                            </h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="••••••••">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted font-weight-bold">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required placeholder="••••••••">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Right: Guidelines / Info Checklist Card --}}
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm theme-card-bg h-100" style="border-radius:18px !important;">
                <div class="card-body p-4 p-md-5 d-flex flex-column">
                    <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
                        <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family:'Outfit',sans-serif;">
                            <i class="bi bi-clipboard-check text-success mr-2" style="font-size:1.25rem;margin-right:8px;"></i>
                            Courier Onboarding Checklist
                        </h5>
                    </div>

                    <p class="text-muted small mb-4" style="line-height:1.45;">
                        Follow these system guidelines to ensure the new driver is correctly registered and configured for live dispatch tracking.
                    </p>

                    <div class="flex-grow-1">
                        <div class="checklist-item">
                            <div class="checklist-icon bg-soft-primary">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold text-theme-dark-bold mb-1" style="font-size:0.85rem;">Unique email credential</h6>
                                <p class="text-muted small mb-0">The email address acts as the driver's username for logging into the courier mobile dispatch application.</p>
                            </div>
                        </div>

                        <div class="checklist-item">
                            <div class="checklist-icon bg-soft-success">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold text-theme-dark-bold mb-1" style="font-size:0.85rem;">GPS Telemetry capability</h6>
                                <p class="text-muted small mb-0">Upon logging in on their device, the driver's GPS location will sync live on the customer tracking maps.</p>
                            </div>
                        </div>

                        <div class="checklist-item">
                            <div class="checklist-icon bg-soft-warning">
                                <i class="bi bi-phone"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold text-theme-dark-bold mb-1" style="font-size:0.85rem;">Contact verification</h6>
                                <p class="text-muted small mb-0">Ensure the phone number is active. Customers receive delivery updates and call options using this number.</p>
                            </div>
                        </div>

                        <div class="checklist-item">
                            <div class="checklist-icon bg-soft-danger">
                                <i class="bi bi-shield-exclamation"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold text-theme-dark-bold mb-1" style="font-size:0.85rem;">Secure password creation</h6>
                                <p class="text-muted small mb-0">Provide a strong password. Drivers can update their security credentials from their own profile panel later.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between text-muted small" style="opacity: 0.8;">
                        <span>Driver Default Role:</span>
                        <span class="badge bg-soft-primary font-weight-bold px-2 py-1" style="border-radius:10px;">courier_fleet</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Floating Action Bar --}}
<div class="floating-save-bar d-flex align-items-center justify-content-between px-4 py-3">
    <div class="d-flex align-items-center" style="font-family:'Outfit',sans-serif;gap:8px;">
        <span class="pulse-green"></span>
        <span class="text-muted text-uppercase d-none d-sm-inline" style="font-size:0.68rem;letter-spacing:0.5px;font-weight:600;white-space:nowrap;">New Registration:</span>
        <span class="font-weight-bold text-nowrap floating-bar-title" style="font-size:0.85rem;">Pending Save</span>
    </div>
    <div class="button-group">
        <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-light">Cancel</a>
        <button type="submit" form="driver-create-form" class="btn btn-primary">
            <i class="bi bi-check2-circle" style="margin-right:6px;"></i> Create Driver
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    'use strict';
    // Interactive feedback: Update floating title dynamic text when typing
    var nameInput = $('input[name="name"]');
    var barTitle  = $('.floating-bar-title');
    
    nameInput.on('input', function() {
        var val = $(this).val().trim();
        if (val.length > 0) {
            barTitle.text(val);
        } else {
            barTitle.text('Pending Save');
        }
    });
});
</script>
@endpush
