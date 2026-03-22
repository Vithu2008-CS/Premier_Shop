@extends('layouts.admin_noble')

@section('title', 'Admin Profile')

@section('content')
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Profile</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Profile Information</h6>
                <div class="mt-3">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Update Password</h6>
                <div class="mt-3">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card border-danger">
            <div class="card-body">
                <h6 class="card-title text-danger">Delete Account</h6>
                <div class="mt-3">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Ensure the customer buttons look okay in admin if btn-add-cart is missing */
    .btn-add-cart {
        background-color: #727cf5 !important;
        border-color: #727cf5 !important;
        color: #fff !important;
    }
    .btn-add-cart:hover {
        background-color: #6169d0 !important;
        border-color: #6169d0 !important;
    }
</style>
@endsection
