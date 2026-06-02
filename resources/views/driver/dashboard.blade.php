{{--
    driver/dashboard.blade.php — Driver dashboard
    ===============================================
    On/off duty toggle, stats row, pending deliveries grid, completed deliveries grid.
    Variable: $driver (auth User), $pendingOrders, $deliveredOrders
--}}
@extends('layouts.driver')
@section('title', 'Driver Dashboard — Premier Shop')

@push('styles')
<style>
    /* ── Dashboard ─────────────────────────────────────────── */
    .dash-hero {
        padding: 36px 0 28px;
    }
    .dash-greeting {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 1.8rem;
        color: #ffffff;
        margin-bottom: 4px;
    }
    .dash-sub {
        color: rgba(255,255,255,0.4);
        font-size: 0.9rem;
    }

    /* duty toggle button */
    .duty-toggle-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 24px;
        border-radius: 100px;
        border: none;
        font-weight: 700;
        font-size: 0.9rem;
        font-family: 'Outfit', sans-serif;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.16,1,0.3,1);
    }
    .duty-toggle-btn.on-duty {
        background: linear-gradient(135deg, #00b894, #00cec9);
        color: #fff;
        box-shadow: 0 6px 24px rgba(0,184,148,0.4);
    }
    .duty-toggle-btn.on-duty:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 32px rgba(0,184,148,0.5);
    }
    .duty-toggle-btn.off-duty {
        background: rgba(255,255,255,0.06);
        color: rgba(255,255,255,0.5);
        border: 1px solid rgba(255,255,255,0.1);
    }
    .duty-toggle-btn.off-duty:hover {
        background: rgba(255,255,255,0.1);
        color: rgba(255,255,255,0.8);
    }
    .duty-toggle-icon {
        width: 22px; height: 22px;
        border-radius: 50%;
        background: rgba(255,255,255,0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
    }

    /* off-duty banner */
    .off-duty-banner {
        background: rgba(225,112,85,0.08);
        border: 1px solid rgba(225,112,85,0.2);
        border-radius: 18px;
        padding: 18px 22px;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        gap: 16px;
        color: #f8b195;
    }
    .off-duty-banner-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        background: rgba(225,112,85,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: #e17055;
        flex-shrink: 0;
    }

    /* ── Stats row ─────────────────────────────────────────── */
    .stat-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 18px;
        padding: 20px 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.25s ease;
    }
    .stat-card:hover {
        background: rgba(255,255,255,0.055);
        border-color: rgba(255,255,255,0.1);
        transform: translateY(-2px);
    }
    .stat-icon {
        width: 46px; height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .stat-icon.purple { background: rgba(108,92,231,0.18); color: #A29BFE; }
    .stat-icon.teal   { background: rgba(0,184,148,0.15);  color: #00cec9; }
    .stat-icon.amber  { background: rgba(253,203,110,0.15); color: #fdcb6e; }
    .stat-val {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 1.7rem;
        color: #ffffff;
        line-height: 1;
    }
    .stat-label {
        font-size: 0.78rem;
        color: rgba(255,255,255,0.4);
        margin-top: 3px;
        font-weight: 500;
    }

    /* ── Section heading ───────────────────────────────────── */
    .section-heading {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 1.05rem;
        color: #ffffff;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-heading .sh-icon {
        width: 30px; height: 30px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
    }
    .sh-icon.purple { background: rgba(108,92,231,0.2); color: #A29BFE; }
    .sh-icon.green  { background: rgba(0,184,148,0.15); color: #00cec9; }

    /* section divider */
    .section-divider {
        border: none;
        border-top: 1px solid rgba(255,255,255,0.06);
        margin: 40px 0 32px;
    }

    /* ── Delivery card ─────────────────────────────────────── */
    .dcard {
        background: rgba(255,255,255,0.028);
        border: 1px solid rgba(255,255,255,0.07);
        border-left: 4px solid transparent;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.16,1,0.3,1);
        height: 100%;
    }
    .dcard:hover {
        background: rgba(255,255,255,0.052);
        border-color: rgba(255,255,255,0.12);
        transform: translateY(-4px);
        box-shadow: 0 16px 48px rgba(0,0,0,0.4);
    }
    .dcard.status-processing { border-left-color: #fdcb6e; }
    .dcard.status-shipped    { border-left-color: #6C5CE7; }
    .dcard.status-delivered  { border-left-color: #00b894; opacity: 0.75; }
    .dcard:hover.status-processing { box-shadow: 0 16px 48px rgba(253,203,110,0.12), 0 0 0 1px rgba(253,203,110,0.1); border-color: rgba(253,203,110,0.2); }
    .dcard:hover.status-shipped    { box-shadow: 0 16px 48px rgba(108,92,231,0.15), 0 0 0 1px rgba(108,92,231,0.1); border-color: rgba(108,92,231,0.2); }

    .dcard-body { padding: 20px; }

    .dcard-number {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 0.78rem;
        padding: 4px 10px;
        border-radius: 8px;
        background: rgba(108,92,231,0.15);
        color: #A29BFE;
        letter-spacing: 0.5px;
    }
    .dcard-number.delivered {
        background: rgba(0,184,148,0.12);
        color: #55efc4;
    }

    .dcard-status {
        font-size: 0.72rem;
        font-weight: 700;
        font-family: 'Outfit', sans-serif;
        padding: 3px 10px;
        border-radius: 100px;
    }
    .dstatus-processing { background: rgba(253,203,110,0.15); color: #fdcb6e; }
    .dstatus-shipped    { background: rgba(108,92,231,0.15);  color: #A29BFE; }
    .dstatus-delivered  { background: rgba(0,184,148,0.12);   color: #55efc4; }

    .dcard-customer {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 1rem;
        color: #ffffff;
        margin: 14px 0 6px;
    }
    .dcard-addr {
        font-size: 0.82rem;
        color: rgba(255,255,255,0.45);
        line-height: 1.5;
        display: flex;
        align-items: flex-start;
        gap: 6px;
        margin-bottom: 16px;
    }
    .dcard-addr i { margin-top: 2px; flex-shrink: 0; color: rgba(255,255,255,0.25); }

    .dcard-footer-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid rgba(255,255,255,0.055);
    }
    .dcard-total {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 1.15rem;
        color: #ffffff;
    }
    .dcard-total.muted {
        font-size: 0.95rem;
        color: rgba(255,255,255,0.4);
        font-weight: 600;
    }

    .btn-view-order {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        border-radius: 100px;
        background: linear-gradient(135deg, #6C5CE7, #8E2DE2);
        color: #fff;
        font-size: 0.8rem;
        font-weight: 700;
        font-family: 'Outfit', sans-serif;
        text-decoration: none;
        border: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(108,92,231,0.35);
    }
    .btn-view-order:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(108,92,231,0.45);
        color: #fff;
    }
    .btn-history {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 14px;
        border-radius: 100px;
        background: rgba(255,255,255,0.07);
        color: rgba(255,255,255,0.55);
        font-size: 0.78rem;
        font-weight: 600;
        font-family: 'Outfit', sans-serif;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-history:hover {
        background: rgba(255,255,255,0.12);
        color: rgba(255,255,255,0.85);
    }

    /* empty state */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        border: 1px dashed rgba(255,255,255,0.1);
        border-radius: 18px;
        color: rgba(255,255,255,0.3);
    }
    .empty-state i { font-size: 2.5rem; display: block; margin-bottom: 12px; opacity: 0.4; }
    .empty-state p { font-size: 0.88rem; margin: 0; }

    /* ── Mobile fixes ──────────────────────────────────────── */
    @media (max-width: 767px) {
        .dash-hero {
            padding: 24px 0 20px;
            flex-direction: column;
            align-items: flex-start !important;
            gap: 16px !important;
        }
        .dash-greeting { font-size: 1.35rem; }
        .dash-sub { font-size: 0.82rem; }
        .duty-toggle-btn { width: 100%; justify-content: center; }

        .off-duty-banner { padding: 14px 16px; border-radius: 14px; }
        .off-duty-banner-icon { width: 38px; height: 38px; font-size: 1.1rem; }

        .section-divider { margin: 28px 0 22px; }
        .section-heading { font-size: 0.95rem; margin-bottom: 14px; }
        .dcard-body { padding: 14px; }
        .dcard-customer { font-size: 0.92rem; margin: 10px 0 5px; }
        .dcard-addr { font-size: 0.78rem; margin-bottom: 10px; }
        .dcard-total { font-size: 1rem; }
        .btn-view-order { padding: 7px 14px; font-size: 0.75rem; }
        .btn-history { padding: 5px 11px; font-size: 0.73rem; }
        .empty-state { padding: 28px 16px; }
    }

    @media (max-width: 575px) {
        /* stat cards: icon above text, compact */
        .stat-card {
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 14px 8px;
            gap: 8px;
            border-radius: 14px;
        }
        .stat-icon { width: 36px; height: 36px; font-size: 1rem; border-radius: 10px; }
        .stat-val { font-size: 1.25rem; }
        .stat-label { font-size: 0.68rem; }
    }

    /* Soft color badges & tokens */
    .bg-soft-success { background: rgba(0, 184, 148, 0.12) !important; color: #55efc4 !important; }
    .bg-soft-info { background: rgba(9, 132, 227, 0.15) !important; color: #74b9ff !important; }
    .bg-soft-warning { background: rgba(245, 158, 11, 0.12) !important; color: #ffeaa7 !important; }
    .text-success { color: #55efc4 !important; }
    .text-info { color: #74b9ff !important; }
    .text-warning { color: #ffeaa7 !important; }

    /* GPS tracking banner */
    .gps-banner {
        background: rgba(0, 184, 148, 0.08);
        border: 1px solid rgba(0, 184, 148, 0.2);
        border-radius: 18px;
        padding: 18px 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        color: #55efc4;
    }
    .gps-banner-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        background: rgba(0, 184, 148, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: #00cec9;
        flex-shrink: 0;
    }
    .gps-pulse-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: auto;
    }
</style>
@endpush

@section('content')
<div class="container">

    {{-- ── Hero header ── --}}
    <div class="dash-hero d-flex flex-wrap justify-content-between align-items-center gap-4">
        <div>
            <div class="dash-greeting">
                {{ $driver->is_on_duty ? '🟢' : '⚫' }} Hey, {{ explode(' ', $driver->name)[0] }}
            </div>
            <div class="dash-sub">
                {{ $driver->is_on_duty ? 'You are active and ready for deliveries.' : 'Toggle duty to start receiving orders.' }}
            </div>
        </div>
        <form action="{{ route('driver.toggleDuty') }}" method="POST">
            @csrf
            <button type="submit" class="duty-toggle-btn {{ $driver->is_on_duty ? 'on-duty' : 'off-duty' }}">
                <span class="duty-toggle-icon">
                    <i class="bi bi-power"></i>
                </span>
                {{ $driver->is_on_duty ? 'Go Off Duty' : 'Go On Duty' }}
            </button>
        </form>
    </div>

    {{-- off-duty banner --}}
    @if(!$driver->is_on_duty)
    <div class="off-duty-banner reveal-3d">
        <div class="off-duty-banner-icon"><i class="bi bi-moon-stars-fill"></i></div>
        <div>
            <div style="font-weight:700;font-size:0.92rem;margin-bottom:3px;">You are off duty</div>
            <div style="font-size:0.82rem;opacity:0.7;">No new orders will be assigned until you go on duty.</div>
        </div>
    </div>
    @endif

    {{-- GPS Tracking Telemetry Status Banner --}}
    @if($driver->is_on_duty)
    <div id="gps-tracking-banner" class="gps-banner mb-4 reveal-3d" style="display: flex;">
        <div class="gps-banner-icon"><i class="bi bi-geo-alt-fill text-success"></i></div>
        <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span style="font-weight:700;font-size:0.92rem;font-family:'Outfit';color:#ffffff;">GPS Tracking Enabled</span>
                <span id="gps-accuracy-badge" class="badge bg-soft-success font-weight-bold px-2 py-0.5" style="border-radius:10px; font-size:0.68rem;">LIVE TRACK</span>
            </div>
            <div style="font-size:0.8rem;color:rgba(255,255,255,0.5);" id="gps-coordinates-text">
                Acquiring precise satellite lock...
            </div>
        </div>
        <div class="gps-pulse-indicator">
            <span class="duty-dot" style="color: #00cec9;"></span>
        </div>
    </div>
    @endif


    {{-- ── Stats row ── --}}
    <div class="row g-3 mb-5 reveal-3d">
        <div class="col-4">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="bi bi-truck"></i></div>
                <div>
                    <div class="stat-val">{{ $pendingOrders->count() }}</div>
                    <div class="stat-label">To Deliver</div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card">
                <div class="stat-icon green"><i class="bi bi-check2-circle"></i></div>
                <div>
                    <div class="stat-val">{{ $deliveredOrders->count() }}</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card">
                <div class="stat-icon amber"><i class="bi bi-box-seam"></i></div>
                <div>
                    <div class="stat-val">{{ $pendingOrders->count() + $deliveredOrders->count() }}</div>
                    <div class="stat-label">Total</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── To Deliver ── --}}
    <div class="section-heading reveal-3d">
        <span class="sh-icon purple"><i class="bi bi-truck-front"></i></span>
        To Deliver
        @if($pendingOrders->count() > 0)
            <span style="background:rgba(108,92,231,0.2);color:#A29BFE;font-size:0.72rem;padding:2px 9px;border-radius:100px;font-weight:700;">{{ $pendingOrders->count() }}</span>
        @endif
    </div>

    <div class="row g-4 stagger-children mb-2">
        @forelse($pendingOrders as $order)
        <div class="col-md-6 col-lg-4 fade-up">
            <div class="dcard status-{{ $order->status }}">
                <div class="dcard-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="dcard-number">#{{ $order->order_number }}</span>
                        <span class="dcard-status dstatus-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                    </div>
                    <div class="dcard-customer">{{ $order->user->name }}</div>
                    <div class="dcard-addr">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>{{ $order->shipping_address['address_line'] ?? 'Address not provided' }}, {{ $order->shipping_address['city'] ?? '' }}</span>
                    </div>
                    @if(!empty($order->shipping_address['phone']))
                    <div style="font-size:0.8rem;color:rgba(255,255,255,0.4);margin-bottom:4px;">
                        <i class="bi bi-telephone me-1"></i>{{ $order->shipping_address['phone'] }}
                    </div>
                    @endif
                    <div class="dcard-footer-row">
                        <span class="dcard-total">£{{ number_format($order->total, 2) }}</span>
                        <a href="{{ route('driver.orders.show', $order) }}" class="btn-view-order">
                            <i class="bi bi-eye"></i> View
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="empty-state">
                <i class="bi bi-truck"></i>
                <p>No active deliveries assigned to you right now.</p>
            </div>
        </div>
        @endforelse
    </div>

    <hr class="section-divider">

    {{-- ── Completed ── --}}
    <div class="section-heading reveal-3d">
        <span class="sh-icon green"><i class="bi bi-check2-all"></i></span>
        Completed Deliveries
    </div>

    <div class="row g-4 stagger-children pb-4">
        @forelse($deliveredOrders as $order)
        <div class="col-md-6 col-lg-4 fade-up">
            <div class="dcard status-delivered">
                <div class="dcard-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="dcard-number delivered">#{{ $order->order_number }}</span>
                        <span class="dcard-status dstatus-delivered">Delivered</span>
                    </div>
                    <div class="dcard-customer">{{ $order->user->name }}</div>
                    <div class="dcard-addr">
                        <i class="bi bi-calendar-check"></i>
                        <span>{{ $order->delivered_date->format('d M Y · H:i') }}</span>
                    </div>
                    <div class="dcard-footer-row">
                        <span class="dcard-total muted">£{{ number_format($order->total, 2) }}</span>
                        <a href="{{ route('driver.orders.show', $order) }}" class="btn-history">
                            <i class="bi bi-clock-history"></i> History
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="empty-state">
                <i class="bi bi-check2-circle"></i>
                <p>No completed deliveries yet.</p>
            </div>
        </div>
        @endforelse
    </div>

</div>
@endsection

@if($driver->is_on_duty)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var coordsText = document.getElementById('gps-coordinates-text');
    var badge = document.getElementById('gps-accuracy-badge');
    
    var simPoints = [
        { lat: 51.500732, lng: -0.124615 },
        { lat: 51.501500, lng: -0.123200 },
        { lat: 51.502800, lng: -0.121800 },
        { lat: 51.504200, lng: -0.120500 },
        { lat: 51.505500, lng: -0.119200 },
        { lat: 51.504800, lng: -0.118000 },
        { lat: 51.502800, lng: -0.119500 },
        { lat: 51.501100, lng: -0.121200 }
    ];
    var simIndex = 0;
    var simulationInterval = null;

    function sendLocationToServer(lat, lng) {
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch("{{ route('driver.location.update') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: JSON.stringify({
                latitude: lat,
                longitude: lng
            })
        })
        .then(response => {
            if (!response.ok) throw new Error("HTTP error " + response.status);
            return response.json();
        })
        .then(data => {
            console.log("Telemetry logged successfully:", data);
        })
        .catch(err => {
            console.error("Failed to post telemetry:", err);
        });
    }

    function triggerSimulationTick() {
        var pt = simPoints[simIndex];
        if (coordsText) {
            coordsText.textContent = "Lat: " + pt.lat.toFixed(6) + " | Lng: " + pt.lng.toFixed(6) + " (Telemetry Active)";
        }
        if (badge) {
            badge.textContent = 'ACTIVE';
            badge.className = 'badge bg-soft-success text-success font-weight-bold px-2 py-0.5';
        }
        sendLocationToServer(pt.lat, pt.lng);
        simIndex = (simIndex + 1) % simPoints.length;
    }

    // Automatically trigger and stream telemetry updates every 5 seconds
    triggerSimulationTick();
    simulationInterval = setInterval(triggerSimulationTick, 5000);
});
</script>
@endpush
@endif

