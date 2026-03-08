@extends('layouts.app')

@section('title', 'My Profile - Premier Shop')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-4"><i class="bi bi-person-gear me-2"></i>My Profile</h2>

            <div class="space-y-6">
                {{-- Update Profile Information --}}
                <div class="card mb-4 shadow-sm">
                    <div class="card-body p-4 p-sm-5">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                {{-- Update Password --}}
                <div class="card mb-4 shadow-sm">
                    <div class="card-body p-4 p-sm-5">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                {{-- Delete Account --}}
                <div class="card shadow-sm border-danger">
                    <div class="card-body p-4 p-sm-5">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection