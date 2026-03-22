@extends('layouts.admin_noble')
@section('title', 'Edit Driver')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.drivers.index') }}">Drivers</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit: {{ $driver->name }}</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Update Driver Information</h6>
        
        <form action="{{ route('admin.drivers.update', $driver) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $driver->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $driver->email) }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $driver->phone) }}" required>
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob', $driver->dob ? $driver->dob->format('Y-m-d') : '') }}" required>
                    @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Home Address</label>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $driver->address) }}</textarea>
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <hr class="my-4">
            <h6 class="card-title">Security Settings</h6>
            <div class="alert alert-fill-light border mb-4">
                <p class="small mb-0 text-muted">
                    <i data-feather="info" class="icon-sm mr-2"></i>
                    Leave the password fields blank if you do not wish to change the current password.
                </p>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">New Password (Optional)</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary mr-2">
                    <i data-feather="save" class="icon-sm mr-2"></i> Update Driver Details
                </button>
                <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
