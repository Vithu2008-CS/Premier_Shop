@extends('layouts.admin')
@section('title', 'Add New Driver — Admin')

@section('content')
<div class="admin-topbar">
    <div>
        <h2>Add New Driver</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.drivers.index') }}">Drivers</a></li>
                <li class="breadcrumb-item active">Add New</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.drivers.index') }}" class="btn btn-admin-outline"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<div class="admin-card">
    <div class="card-title">Driver Information</div>
    
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.drivers.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="dob" class="form-control" value="{{ old('dob') }}" required>
            </div>
            <div class="col-12">
                <label class="form-label">Home Address</label>
                <textarea name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <div class="col-12 text-end mt-4">
                <button type="submit" class="btn btn-admin">Create Driver Account</button>
            </div>
        </div>
    </form>
</div>
@endsection
