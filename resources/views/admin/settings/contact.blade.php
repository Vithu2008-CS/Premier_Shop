@extends('layouts.admin_noble')
@section('title', 'Contact & Social Settings')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Contact & Social Settings</li>
  </ol>
</nav>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i data-feather="check-circle" class="icon-md mr-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i data-feather="alert-circle" class="icon-md mr-2"></i>
    <strong>Please correct the following errors:</strong>
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<form action="{{ route('admin.settings.contact.store') }}" method="POST">
    @csrf
    <div class="row">
        {{-- Contact Details Card --}}
        <div class="col-lg-8">
            <div class="card grid-margin stretch-card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-body">
                    <h6 class="card-title text-primary mb-4 d-flex align-items-center">
                        <i data-feather="phone" class="icon-md me-2"></i> Shop Contact Information
                    </h6>
                    
                    <div class="row">
                        {{-- Phone Number --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-telephone text-muted"></i></span>
                                <input type="text" name="contact_phone" class="form-control" 
                                       value="{{ old('contact_phone', $settings->other_settings['contact_phone'] ?? '+44 770 000 0000') }}" 
                                       placeholder="E.g., +44 770 000 0000" required>
                            </div>
                        </div>

                        {{-- Phone Availability --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Phone Availability Note</label>
                            <input type="text" name="contact_phone_availability" class="form-control" 
                                   value="{{ old('contact_phone_availability', $settings->other_settings['contact_phone_availability'] ?? 'Available 24/7 for support') }}" 
                                   placeholder="E.g., Available 24/7 for support" required>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Support Email --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Support Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" name="contact_email" class="form-control" 
                                       value="{{ old('contact_email', $settings->other_settings['contact_email'] ?? 'info@premiershop.com') }}" 
                                       placeholder="E.g., support@premiershop.com" required>
                            </div>
                        </div>

                        {{-- Email Availability --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Email Availability Note</label>
                            <input type="text" name="contact_email_availability" class="form-control" 
                                   value="{{ old('contact_email_availability', $settings->other_settings['contact_email_availability'] ?? 'We reply within 24 hours') }}" 
                                   placeholder="E.g., We reply within 24 hours" required>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Store Address --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Physical Store Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-geo-alt text-muted"></i></span>
                                <input type="text" name="contact_address" class="form-control" 
                                       value="{{ old('contact_address', $settings->other_settings['contact_address'] ?? 'London, United Kingdom') }}" 
                                       placeholder="E.g., London, United Kingdom" required>
                            </div>
                        </div>

                        {{-- Store Hours --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Store Opening Hours Note</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-clock text-muted"></i></span>
                                <input type="text" name="contact_hours" class="form-control" 
                                       value="{{ old('contact_hours', $settings->other_settings['contact_hours'] ?? 'Open Mon–Sat, 9 AM – 6 PM') }}" 
                                       placeholder="E.g., Open Mon–Sat, 9 AM – 6 PM" required>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Social Media Accounts Card --}}
        <div class="col-lg-4">
            <div class="card grid-margin stretch-card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-body">
                    <h6 class="card-title text-primary mb-4 d-flex align-items-center">
                        <i data-feather="share-2" class="icon-md me-2"></i> Social Media Handles
                    </h6>
                    
                    {{-- Facebook --}}
                    <div class="mb-3">
                        <label class="form-label font-weight-bold d-flex align-items-center">
                            <i class="bi bi-facebook text-primary me-2 fs-5"></i> Facebook URL
                        </label>
                        <input type="url" name="social_facebook" class="form-control" 
                               value="{{ old('social_facebook', $settings->other_settings['social_facebook'] ?? '#') }}" 
                               placeholder="E.g., https://facebook.com/my-shop">
                    </div>

                    {{-- Instagram --}}
                    <div class="mb-3">
                        <label class="form-label font-weight-bold d-flex align-items-center">
                            <i class="bi bi-instagram text-danger me-2 fs-5"></i> Instagram URL
                        </label>
                        <input type="url" name="social_instagram" class="form-control" 
                               value="{{ old('social_instagram', $settings->other_settings['social_instagram'] ?? '#') }}" 
                               placeholder="E.g., https://instagram.com/my-shop">
                    </div>

                    {{-- Twitter / X --}}
                    <div class="mb-3">
                        <label class="form-label font-weight-bold d-flex align-items-center">
                            <i class="bi bi-twitter-x text-dark me-2 fs-5"></i> Twitter/X URL
                        </label>
                        <input type="url" name="social_twitter" class="form-control" 
                               value="{{ old('social_twitter', $settings->other_settings['social_twitter'] ?? '#') }}" 
                               placeholder="E.g., https://x.com/my-shop">
                    </div>

                    {{-- TikTok --}}
                    <div class="mb-4">
                        <label class="form-label font-weight-bold d-flex align-items-center">
                            <i class="bi bi-tiktok text-dark me-2 fs-5"></i> TikTok URL
                        </label>
                        <input type="url" name="social_tiktok" class="form-control" 
                               value="{{ old('social_tiktok', $settings->other_settings['social_tiktok'] ?? '#') }}" 
                               placeholder="E.g., https://tiktok.com/@my-shop">
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill fw-bold pb-2 pt-2 shadow-sm">
                            <i data-feather="save" class="icon-sm me-2"></i> Save Dynamic Contact Info
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>
@endsection
