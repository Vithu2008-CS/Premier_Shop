{{--
    admin/audit-logs/index.blade.php — Admin action audit trail
    ============================================================
    Read-only, filterable table of every state-changing admin request.
    Rows are written automatically by the AuditAdminActions middleware.
    Variables: $logs (paginated), $staffUsers (actor filter dropdown)
--}}
@extends('layouts.admin_noble')
@section('title', 'Audit Logs')

@push('styles')
<style>
.rounded-4 { border-radius: 18px !important; }
.rounded-3 { border-radius: 12px !important; }

.bg-soft-success   { background: rgba(16,185,129,0.1) !important; color: #10b981 !important; }
.bg-soft-warning   { background: rgba(245,158,11,0.1) !important; color: #f59e0b !important; }
.bg-soft-danger    { background: rgba(239,68,68,0.1) !important; color: #ef4444 !important; }
.bg-soft-info      { background: rgba(6,182,212,0.1) !important; color: #06b6d4 !important; }
.bg-soft-secondary { background: rgba(100,116,139,0.1) !important; color: #64748b !important; }

html[data-admin-theme="light"] .theme-card-bg { background-color: #ffffff !important; }
html[data-admin-theme="dark"]  .theme-card-bg { background-color: #0c1427 !important; border: 1px solid rgba(255,255,255,0.05) !important; }

.payload-pre {
    max-height: 220px;
    overflow: auto;
    font-size: 0.78rem;
    white-space: pre-wrap;
    word-break: break-all;
    margin-bottom: 0;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-2">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Audit Logs</h4>
            <p class="text-muted small mb-0">Trail of all state-changing admin actions. Read-only.</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card theme-card-bg rounded-4 shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Search action / URL</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="e.g. products.update">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Actor</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">All staff</option>
                        @foreach($staffUsers as $staff)
                            <option value="{{ $staff->id }}" @selected(request('user_id') == $staff->id)>{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Method</label>
                    <select name="method" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach(['POST', 'PUT', 'PATCH', 'DELETE'] as $method)
                            <option value="{{ $method }}" @selected(request('method') === $method)>{{ $method }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Log table --}}
    <div class="card theme-card-bg rounded-4 shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="small text-muted">
                            <th class="ps-3">When</th>
                            <th>Actor</th>
                            <th>Action</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>IP</th>
                            <th class="pe-3">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="ps-3 small text-nowrap" title="{{ $log->created_at }}">
                                    {{ $log->created_at->format('d M Y H:i:s') }}
                                </td>
                                <td class="small">{{ $log->user->name ?? 'Unknown / deleted' }}</td>
                                <td class="small"><code>{{ $log->action }}</code></td>
                                <td>
                                    @php
                                        $methodClass = [
                                            'POST'   => 'bg-soft-success',
                                            'PUT'    => 'bg-soft-info',
                                            'PATCH'  => 'bg-soft-warning',
                                            'DELETE' => 'bg-soft-danger',
                                        ][$log->method] ?? 'bg-soft-secondary';
                                    @endphp
                                    <span class="badge {{ $methodClass }}">{{ $log->method }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $log->status < 400 ? 'bg-soft-success' : 'bg-soft-danger' }}">{{ $log->status }}</span>
                                </td>
                                <td class="small">{{ $log->ip_address }}</td>
                                <td class="pe-3">
                                    @if(!empty($log->payload))
                                        <button class="btn btn-sm btn-outline-secondary py-0" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#payload-{{ $log->id }}">
                                            Payload
                                        </button>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                            @if(!empty($log->payload))
                                <tr class="collapse" id="payload-{{ $log->id }}">
                                    <td colspan="7" class="ps-3">
                                        <div class="mb-1 small text-muted text-break">{{ $log->url }}</div>
                                        <pre class="payload-pre p-2 rounded-3 bg-body-tertiary">{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">No audit log entries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
            <div class="card-footer bg-transparent border-0 py-3">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
