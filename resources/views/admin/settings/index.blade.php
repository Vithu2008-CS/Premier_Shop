@extends('layouts.admin')
@section('title', 'Settings — Admin Dashboard')

@section('content')
<div class="admin-topbar">
    <div>
        <h2>System Settings</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Settings</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card">
            <h5 class="card-title mb-4"><i class="bi bi-shop me-2"></i>Shop Opening Hours</h5>
            
            <form action="{{ route('admin.settings.store') }}" method="POST">
                @csrf
                
                <div class="table-responsive">
                    <table class="table admin-table mb-4">
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
                                @php
                                    $openKey = "shop_hours_{$day}_open";
                                    $closeKey = "shop_hours_{$day}_close";
                                    $closedKey = "shop_hours_{$day}_closed";
                                    $openTime = $settings[$openKey] ?? '';
                                    $closeTime = $settings[$closeKey] ?? '';
                                    $isClosed = filter_var($settings[$closedKey] ?? 'false', FILTER_VALIDATE_BOOLEAN);
                                @endphp
                                <tr>
                                    <td class="fw-bold text-capitalize align-middle">{{ $day }}</td>
                                    <td>
                                        <input type="time" name="{{ $openKey }}" class="form-control form-control-sm" value="{{ $openTime }}" {{ $isClosed ? 'disabled' : '' }}>
                                    </td>
                                    <td>
                                        <input type="time" name="{{ $closeKey }}" class="form-control form-control-sm" value="{{ $closeTime }}" {{ $isClosed ? 'disabled' : '' }}>
                                    </td>
                                    <td class="align-middle">
                                        <div class="form-check form-switch mb-0">
                                            <input type="hidden" name="{{ $closedKey }}" value="false">
                                            <input class="form-check-input closed-toggle" type="checkbox" name="{{ $closedKey }}" value="true" {{ $isClosed ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mb-4">
                    <label class="form-label">Shop Notice Banner (Optional, displayed on homepage)</label>
                    <textarea name="shop_notice" class="form-control" rows="2" placeholder="E.g., Special holiday hours in effect!">{{ $settings['shop_notice'] ?? '' }}</textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-admin"><i class="bi bi-save me-2"></i>Save Settings</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="admin-card text-center py-5">
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center" style="width:80px;height:80px;border-radius:50%;background:rgba(108,92,231,0.1);color:#6C5CE7;">
                    <i class="bi bi-info-circle fs-1"></i>
                </div>
            </div>
            <h5 class="fw-bold text-white mb-3">Shop Hours Format</h5>
            <p class="text-muted small mb-0 px-3">
                Toggle "Closed" if the shop is closed for the entire day. These hours are displayed on the welcome page for customers. Use 24-hour format or AM/PM depending on your browser.
            </p>
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
