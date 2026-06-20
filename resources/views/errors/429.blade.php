{{--
    errors/429.blade.php — 429 Too Many Requests error page
    =======================================================
    Shown when a rate limiter (throttle middleware) rejects an HTML request.
    AJAX/JSON requests receive a JSON 429 automatically and never hit this view.
    Styled with the storefront layout to match 404/500.
--}}
@extends('layouts.app')
@section('title', 'Slow down — Premier Shop')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="mb-5 fade-up">
                    <div class="d-inline-flex align-items-center justify-content-center mb-4" style="width:120px;height:120px;background:var(--ps-gradient);border-radius:35px;box-shadow:var(--ps-shadow-lg);">
                        <i class="bi bi-hourglass-split text-white" style="font-size:3.5rem;"></i>
                    </div>
                    <h1 class="display-1 fw-bold gradient-text mb-2">429</h1>
                    <h2 class="fw-bold text-dark mb-3">Whoa, slow down a moment</h2>
                    <p class="text-muted fs-5 mb-5 px-lg-5">
                        You've made a lot of requests in a short time. This is a safety
                        limit to keep the shop fast for everyone. Please wait
                        @if(! empty($exception) && method_exists($exception, 'getHeaders') && ! empty($exception->getHeaders()['Retry-After']))
                            about {{ $exception->getHeaders()['Retry-After'] }} seconds
                        @else
                            a minute
                        @endif
                        and try again.
                    </p>

                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('home') }}" class="btn btn-add-cart px-4 py-3">
                            <i class="bi bi-house-door me-2"></i> Back to Homepage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
