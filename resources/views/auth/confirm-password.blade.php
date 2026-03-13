@extends('layouts.app')
@section('title', 'Confirm Password — Premier Shop')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center mb-4 fade-up">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;background:var(--ps-gradient);border-radius:20px;">
                        <i class="bi bi-shield-lock-fill text-white" style="font-size:2rem;"></i>
                    </div>
                    <h2 class="fw-bold">Confirm Password</h2>
                    <p class="text-muted">This is a secure area of the application. Please confirm your password before continuing.</p>
                </div>

                <div class="card fade-up delay-1">
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf

                            <!-- Password -->
                            <div class="mb-4">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password" placeholder="••••••••">
                                    @error('password') 
                                        <div class="invalid-feedback">{{ $message }}</div> 
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-add-cart w-100">
                                <i class="bi bi-check2-circle me-2"></i> Confirm
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
