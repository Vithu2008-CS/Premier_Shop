{{--
    admin/drivers/index.blade.php — Driver monitoring dashboard
    ============================================================
    Table of all driver accounts: name, email, assigned active orders count, status.
    Links to create new driver. DriverController resource.
    Variable: $drivers (with assigned_orders_count)
--}}
@extends('layouts.admin_noble')
@section('title', 'Driver Monitoring')

@section('content')
<div class="container-fluid px-0" style="padding-bottom: 40px;">
    {{-- Page Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="h3 mb-0 text-gray-800 fw-bold" style="font-family: 'Outfit', sans-serif;">Driver Monitoring</h2>
            <p class="text-muted mb-0">Track on-duty status, review active workloads, and manage delivery driver assignments</p>
        </div>
        <div class="col-md-6 text-right d-none d-md-block">
            <span class="badge bg-soft-primary px-3 py-2 rounded-pill font-weight-bold" style="font-size: 0.8rem;">Total: {{ $drivers->count() }} Drivers</span>
        </div>
    </div>

    {{-- System Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background-color: rgba(16, 185, 129, 0.12); color: #10b981; padding: 0.75rem 1rem;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill mr-2" style="font-size: 1rem;"></i>
                    <span class="font-weight-bold" style="font-size: 0.82rem;">{{ session('success') }}</span>
                </div>
                <button type="button" class="close p-0" data-dismiss="alert" aria-label="Close" style="color: #10b981; opacity: 0.8; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background-color: rgba(239, 68, 68, 0.12); color: #ef4444; padding: 0.75rem 1rem;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill mr-2" style="font-size: 1rem;"></i>
                    <span class="font-weight-bold" style="font-size: 0.82rem;">{{ session('error') }}</span>
                </div>
                <button type="button" class="close p-0" data-dismiss="alert" aria-label="Close" style="color: #ef4444; opacity: 0.8; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    @endif

    {{-- Drivers Dashboard Table Card --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4" style="border-radius: 18px !important;">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family: 'Outfit', sans-serif;">
                <i class="bi bi-truck text-primary mr-2" style="font-size: 1.15rem;"></i>
                Active Fleet Directory
            </h5>
            
            {{-- Add Driver Button in a gorgeous curved design --}}
            <a href="{{ route('admin.drivers.create') }}" class="btn btn-primary btn-curved d-inline-flex align-items-center justify-content-center">
                <i class="bi bi-person-plus-fill mr-2" style="font-size: 0.95rem;"></i>
                Add New Driver
            </a>
        </div>
        
        <div class="card-body p-0 mt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1.5px solid rgba(0, 0, 0, 0.04);">
                            <th class="ps-4 py-3 pl-4 align-middle">Driver</th>
                            <th class="py-3 align-middle d-none d-sm-table-cell">Contact Email</th>
                            <th class="py-3 align-middle">Duty Status</th>
                            <th class="py-3 align-middle">Active Workload</th>
                            <th class="py-3 align-middle d-none d-md-table-cell">Joined Date</th>
                            <th class="py-3 align-middle text-right pr-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drivers as $driver)
                        <tr style="border-bottom: 1px solid rgba(0, 0, 0, 0.02); transition: all 0.2s ease;">
                            {{-- Driver Profile Info --}}
                            <td class="ps-4 pl-4 py-3 align-middle font-weight-bold">
                                <div class="d-flex align-items-center">
                                    <div class="wd-42 h-42 rounded-circle bg-soft-primary text-primary font-weight-bold d-flex align-items-center justify-content-center mr-3 shadow-sm" style="font-size: 1rem; min-width: 42px; height: 42px;">
                                        {{ strtoupper(substr($driver->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.drivers.edit', $driver) }}" class="text-dark-theme-aware text-hover-primary text-decoration-none font-weight-bold" style="font-size: 0.88rem;" title="Edit Driver Details">
                                            {{ $driver->name }}
                                        </a>
                                        {{-- Mobile only email subtext --}}
                                        <span class="d-block d-sm-none text-muted small mt-0.5" style="font-weight: normal; font-size: 0.76rem;">{{ $driver->email }}</span>
                                    </div>
                                </div>
                            </td>
                            
                            {{-- Email Column --}}
                            <td class="align-middle text-muted small d-none d-sm-table-cell" style="font-size: 0.82rem;">
                                {{ $driver->email }}
                            </td>
                            
                            {{-- Duty Status with pulsing indicators --}}
                            <td class="align-middle">
                                @if($driver->is_on_duty)
                                    <span class="badge bg-soft-success d-inline-flex align-items-center font-weight-bold px-2.5 py-1.5" style="font-size: 0.72rem; border-radius: 20px;">
                                        <span class="pulse-green mr-1.5"></span>
                                        ON DUTY
                                    </span>
                                @else
                                    <span class="badge bg-soft-secondary d-inline-flex align-items-center font-weight-bold px-2.5 py-1.5" style="font-size: 0.72rem; border-radius: 20px;">
                                        <span class="indicator-grey mr-1.5"></span>
                                        OFF DUTY
                                    </span>
                                @endif
                            </td>
                            
                            {{-- Workload --}}
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-soft-primary font-weight-extrabold px-3 py-1.5" style="font-size: 0.74rem; border-radius: 20px;">
                                        {{ $driver->processing_orders_count }} Orders
                                    </span>
                                    <span class="text-muted small ml-2 d-none d-md-inline" style="font-size: 0.76rem;">active</span>
                                </div>
                            </td>
                            
                            {{-- Joined Date --}}
                            <td class="align-middle text-muted small d-none d-md-table-cell" style="font-size: 0.82rem;">
                                {{ $driver->created_at->format('d M Y') }}
                            </td>
                            
                            {{-- Actions: Rose outline Delete button ONLY --}}
                            <td class="text-right pr-4 align-middle">
                                <form action="{{ route('admin.drivers.destroy', $driver) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to permanently delete this driver account? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-premium-delete">
                                        <i class="bi bi-trash"></i>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                    <i class="bi bi-truck text-muted mb-3" style="font-size: 2.5rem;"></i>
                                    <p class="text-muted font-weight-bold" style="font-size: 0.9rem;">No drivers registered in the fleet yet.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap');

.container-fluid {
    font-family: 'Inter', sans-serif;
}

/* Curve styles */
.rounded-4 {
    border-radius: 18px !important;
}

/* Soft badges and HSL themed tokens */
.bg-soft-primary { background: rgba(108,92,231,0.1) !important; color: #6c5ce7 !important; }
.bg-soft-success { background: rgba(16,185,129,0.1) !important; color: #10b981 !important; }
.bg-soft-secondary { background: rgba(100,116,139,0.1) !important; color: #64748b !important; }
.bg-soft-danger { background: rgba(239,68,68,0.1) !important; color: #ef4444 !important; }

html[data-admin-theme="dark"] .bg-soft-primary { background: rgba(167, 139, 250, 0.15) !important; color: #a78bfa !important; }
html[data-admin-theme="dark"] .bg-soft-success { background: rgba(52, 211, 153, 0.15) !important; color: #34d399 !important; }
html[data-admin-theme="dark"] .bg-soft-secondary { background: rgba(148, 163, 184, 0.15) !important; color: #94a3b8 !important; }

/* Table hover effects */
tbody tr:hover {
    background-color: rgba(108, 92, 231, 0.015) !important;
}
html[data-admin-theme="dark"] tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.01) !important;
}
html[data-admin-theme="dark"] td {
    color: #cbd5e1 !important;
}
html[data-admin-theme="light"] .text-dark-theme-aware {
    color: #1e293b !important;
}
html[data-admin-theme="dark"] .text-dark-theme-aware {
    color: #cbd5e1 !important;
}

.text-hover-primary:hover {
    color: #6c5ce7 !important;
    text-decoration: none !important;
}
html[data-admin-theme="dark"] .text-hover-primary:hover {
    color: #a78bfa !important;
}

/* Pulsing Status Indicators */
.pulse-green {
    width: 7px;
    height: 7px;
    background-color: #10b981;
    border-radius: 50%;
    display: inline-block;
    animation: pulseG 1.6s infinite ease-in-out;
}
@keyframes pulseG {
    0%, 100% { transform: scale(0.95); opacity: 0.5; }
    50% { transform: scale(1.2); opacity: 1; }
}

.indicator-grey {
    width: 7px;
    height: 7px;
    background-color: #64748b;
    border-radius: 50%;
    display: inline-block;
}

/* Add Driver Button: Curved design */
.btn-curved {
    border-radius: 30px !important;
    padding: 8px 24px !important;
    font-size: 0.85rem !important;
    font-weight: 700 !important;
    font-family: 'Outfit', sans-serif !important;
    background: linear-gradient(135deg, #6c5ce7, #a78bfa) !important;
    border: none !important;
    box-shadow: 0 4px 12px rgba(108, 92, 231, 0.2) !important;
    color: #ffffff !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.btn-curved:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 16px rgba(108, 92, 231, 0.3) !important;
    color: #ffffff !important;
}
.btn-curved:active {
    transform: scale(0.98) !important;
}

/* Premium Outlined Delete Action Button (Matches references) */
.btn-premium-delete {
    background-color: transparent !important;
    border: 1.5px solid #ff3366 !important;
    color: #ff3366 !important;
    border-radius: 50px !important;
    padding: 6px 18px !important;
    font-size: 0.78rem !important;
    font-weight: 700 !important;
    font-family: 'Outfit', sans-serif !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    cursor: pointer !important;
    transition: all 0.2s ease-in-out !important;
    outline: none !important;
    box-shadow: none !important;
    line-height: 1 !important;
}
.btn-premium-delete i {
    font-size: 0.88rem !important;
    line-height: 1 !important;
}
.btn-premium-delete span {
    line-height: 1 !important;
}
.btn-premium-delete:hover {
    background-color: rgba(255, 51, 102, 0.05) !important;
    box-shadow: 0 4px 10px rgba(255, 51, 102, 0.12) !important;
    transform: translateY(-0.5px) !important;
}
.btn-premium-delete:active {
    transform: scale(0.97) !important;
}

/* Dark mode card integration */
html[data-admin-theme="dark"] .card {
    background-color: #0c1427 !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
}

/* General Layout adjustments */
.mr-1.5 { margin-right: 0.38rem; }
.mr-2 { margin-right: 0.5rem; }
.mr-3 { margin-right: 0.75rem; }

@media (max-width: 575px) {
    .btn-curved {
        padding: 6px 16px !important;
        font-size: 0.78rem !important;
    }
    .card-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 12px !important;
    }
    .btn-premium-delete {
        padding: 5px 12px !important;
        font-size: 0.74rem !important;
    }
}
</style>
@endsection
