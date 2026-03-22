@extends('layouts.admin_noble')
@section('title', 'System Settings')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">System Settings</li>
  </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <div class="card grid-margin stretch-card">
            <div class="card-body">
                <h6 class="card-title">General Shop Settings</h6>
                <form action="{{ route('admin.settings.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Shop Name</label>
                            <input type="text" name="shop_name" class="form-control" value="{{ $settings->shop_name }}" placeholder="E.g., Premier Shop">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Store Address (Origin)</label>
                            <input type="text" name="origin_address" class="form-control" value="{{ $settings->origin_address }}" placeholder="Full store address">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="icon-sm mr-2"></i> Update General Info
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card grid-margin stretch-card">
            <div class="card-body">
                <h6 class="card-title">Shop Opening Hours</h6>
                
                <form action="{{ route('admin.settings.store') }}" method="POST">
                    @csrf
                    
                    <div class="table-responsive">
                        <table class="table table-hover mb-4">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Opening Time</th>
                                    <th>Closing Time</th>
                                    <th>Closed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                @endphp
                                @foreach($days as $day)
                                    <tr>
                                        <td class="font-weight-bold text-capitalize align-middle">{{ $day }}</td>
                                        @php
                                            $hours = $settings->other_settings['shop_hours'][$day] ?? [];
                                            $openTime = $hours['open'] ?? '';
                                            $closeTime = $hours['close'] ?? '';
                                            $isClosed = $hours['closed'] ?? false;
                                        @endphp
                                        <td>
                                            <input type="time" name="shop_hours[{{ $day }}][open]" class="form-control form-control-sm" value="{{ $openTime }}" {{ $isClosed ? 'disabled' : '' }}>
                                        </td>
                                        <td>
                                            <input type="time" name="shop_hours[{{ $day }}][close]" class="form-control form-control-sm" value="{{ $closeTime }}" {{ $isClosed ? 'disabled' : '' }}>
                                        </td>
                                        <td class="align-middle">
                                            <div class="form-check form-switch mb-0">
                                                <input type="hidden" name="shop_hours[{{ $day }}][closed]" value="0">
                                                <input class="form-check-input closed-toggle" type="checkbox" name="shop_hours[{{ $day }}][closed]" value="1" {{ $isClosed ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Shop Notice Banner (Optional, displayed on homepage)</label>
                        <textarea name="shop_notice" class="form-control" rows="2" placeholder="E.g., Special holiday hours in effect!">{{ $settings->other_settings['shop_notice'] ?? '' }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="icon-sm mr-2"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card grid-margin stretch-card text-center">
            <div class="card-body py-5">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-soft-primary text-primary" style="width:80px;height:80px;border-radius:50%;">
                        <i data-feather="clock" class="icon-lg"></i>
                    </div>
                </div>
                <h5 class="font-weight-bold mb-3">Shop Hours Format</h5>
                <p class="text-muted small mb-0 px-3">
                    Toggle "Closed" if the shop is closed for the entire day. These hours are displayed on the welcome page for customers. Use 24-hour format or AM/PM depending on your browser.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggles = document.querySelectorAll('.closed-toggle');
        toggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const tr = this.closest('tr');
                const inputs = tr.querySelectorAll('input[type="time"]');
                inputs.forEach(input => {
                    input.disabled = this.checked;
                    if(this.checked) input.value = '';
                });
            });
        });
    });
</script>
@endpush

