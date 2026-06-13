{{--
    orders/show.blade.php — Order detail page (customer view)
    ===========================================================
    Shows: order status timeline, shipping address, line items with prices,
    subtotal / discount / shipping / total breakdown, QR code for the order,
    cancel button (pending only), return request button (delivered only),
    link to existing return request if already submitted.
    Variable: $order (with items.product, user, returnRequest loaded)
--}}
@extends('layouts.app')
@section('title', 'Order ' . $order->order_number . ' - Premier Shop')

@section('content')
<div class="container section-padding">
    <div class="mb-4 reveal-3d">
        <a href="{{ route('orders.index') }}" class="btn btn-link text-decoration-none text-primary ps-0">
            <i class="bi bi-arrow-left me-2"></i>Back to Orders
        </a>
    </div>

    <div class="row g-5">
        {{-- Left Column: Order Items & Details --}}
        <div class="col-lg-8 reveal-slide-left">
            <div class="card order-card shadow-sm border-0">
                <div class="order-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3">
                            <i class="bi bi-receipt text-primary fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0" style="font-family: 'Outfit', sans-serif;">{{ $order->order_number }}</h4>
                            <p class="text-muted small mb-0">Placed on {{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    @php
                        $statusIcons = [
                            'pending' => 'bi-clock-history',
                            'processing' => 'bi-gear-wide-connected',
                            'shipped' => 'bi-truck',
                            'delivered' => 'bi-check2-circle',
                            'cancelled' => 'bi-x-circle'
                        ];
                    @endphp
                    <span class="status-badge status-{{ $order->status }}">
                        <i class="bi {{ $statusIcons[$order->status] ?? 'bi-info-circle' }}"></i>
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Order Items</h5>
                    @foreach($order->items as $item)
                    <div class="d-flex align-items-center justify-content-between border-bottom py-3 hover-link">
                        <div class="d-flex align-items-center gap-3" style="min-width:0;flex:1;overflow:hidden;">
                            <img src="{{ $item->product->first_image }}" alt=""
                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 12px; flex-shrink:0;"
                                 class="shadow-sm" data-fallback-src="/images/placeholder-product.png">
                            <div style="min-width:0;">
                                <h6 class="fw-bold mb-1 text-truncate">{{ $item->product->name }}</h6>
                                <p class="text-muted small mb-0">Qty: {{ $item->quantity }} × £{{ number_format($item->price, 2) }}</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-body fs-5">£{{ number_format($item->price * $item->quantity, 2) }}</span>
                        </div>
                    </div>
                    @endforeach

                    <div class="mt-4 pt-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
                        <a href="{{ route('orders.print', $order) }}" class="btn btn-outline-secondary rounded-pill px-4 hover-up shadow-sm">
                            <i class="bi bi-printer me-2"></i>Invoice
                        </a>
                        
                        @if($order->status === 'delivered')
                            @if($order->returnRequest)
                                <a href="{{ route('returns.show', $order->returnRequest) }}" class="btn btn-primary rounded-pill px-4 hover-up shadow-sm">
                                    <i class="bi bi-arrow-return-left me-2"></i>View Return Status
                                </a>
                            @else
                                <a href="{{ route('returns.create', $order) }}" class="btn btn-outline-danger rounded-pill px-4 hover-up shadow-sm">
                                    <i class="bi bi-box-seam me-2"></i>Request Return
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-1">
                <div class="col-md-6 col-lg-5">
                    <div class="h-100">
                        <h6 class="fw-bold mb-3 text-uppercase small letter-spacing-1">
                            <i class="bi bi-geo-alt-fill text-primary me-2"></i>Delivery To
                        </h6>
                        <div class="p-3 rounded-4 border" style="background: var(--ps-surface-secondary); border-color: var(--ps-border) !important;">
                            <p class="mb-1 fw-bold small">{{ auth()->user()->name }}</p>
                            <p class="mb-0 text-muted small">{{ $order->shipping_address['address_line'] ?? 'N/A' }}</p>
                            <p class="mb-0 text-muted small">{{ $order->shipping_address['city'] ?? '' }}</p>
                            <p class="mt-2 mb-0 small text-primary"><i class="bi bi-telephone-fill me-1"></i> {{ $order->shipping_address['phone'] ?? 'N/A' }}</p>
                            <hr class="my-2 opacity-10">
                            <p class="mb-0 small text-body"><i class="bi bi-credit-card-2-front-fill me-1 text-primary"></i> <strong>Payment:</strong> {{ $order->payment_method ?? 'Debit/Credit Card' }}</p>
                        </div>
                        
                        @if($order->driver && $order->status !== 'shipped')
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3 text-uppercase small letter-spacing-1">
                                <i class="bi bi-person-badge-fill text-primary me-2"></i>
                                @if($order->status === 'delivered')
                                    Delivered By
                                @else
                                    Assigned Driver
                                @endif
                            </h6>
                            <div class="p-3 rounded-4 border d-flex align-items-center justify-content-between" style="background: var(--ps-surface-secondary); border-color: var(--ps-border) !important;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; border: 1px solid var(--ps-primary);">
                                        <i class="bi bi-person-badge text-primary fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 small text-body">{{ $order->driver->name }}</h6>
                                        <span class="text-success small d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                            <span class="d-inline-block rounded-circle bg-success" style="width: 5px; height: 5px;"></span>
                                            {{ $order->status === 'delivered' ? 'Completed' : 'On Duty' }}
                                        </span>
                                    </div>
                                </div>
                                @if($order->driver->phone && $order->status !== 'delivered')
                                <div>
                                    <a href="tel:{{ $order->driver->phone }}" class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border: 1px solid var(--ps-border);">
                                        <i class="bi bi-telephone-fill text-primary small"></i>
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6 col-lg-7">
                    <div class="h-100">
                        <h6 class="fw-bold mb-3 text-uppercase small letter-spacing-1">
                            <i class="bi bi-receipt-cutoff text-primary me-2"></i>Summary
                        </h6>
                        <div class="p-3 rounded-4 border" style="background: var(--ps-surface-secondary); border-color: var(--ps-border) !important;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Subtotal</span>
                                <span class="fw-bold small">£{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span class="small">Discount</span>
                                <span class="fw-bold small">-£{{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                <span class="text-muted small">Shipping</span>
                                <span class="fw-bold small">£{{ number_format($order->shipping_cost, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between pt-1">
                                <span class="fw-bold text-primary">Total Paid</span>
                                <span class="fw-bold text-primary">£{{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            @if($order->status === 'shipped' || $order->status === 'delivered')
                <!-- Google Maps API -->
                <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}" crossorigin=""></script>
                
                <!-- Live Tracking Panel -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius:20px; overflow:hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0 d-flex align-items-center text-body" style="font-family: 'Outfit', sans-serif;">
                                <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                                @if($order->status === 'shipped')
                                    Courier Live Tracking
                                @else
                                    Delivery Route Map
                                @endif
                            </h5>
                            @if($order->status === 'shipped')
                                <span class="badge bg-success bg-opacity-10 text-success d-flex align-items-center gap-1 px-3 py-2 rounded-pill shadow-sm" style="animation: pulse-badge 2s infinite;">
                                    <span class="d-inline-block rounded-circle bg-success" style="width: 8px; height: 8px;"></span> Live
                                </span>
                            @endif
                        </div>
                        
                        <!-- Map Container -->
                        <div id="live-delivery-map" style="height: 230px; border-radius: 16px; border: 1px solid var(--ps-border); margin-bottom: 16px; z-index: 10;" class="shadow-inner"></div>
                        
                        @if($order->status === 'shipped')
                            <!-- Courier Stats and Simulation Messages -->
                            <div class="p-3 rounded-4 mb-3 border border-dashed text-start" style="background: var(--ps-surface-secondary); border-color: var(--ps-border) !important;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                    <strong class="small text-body">Courier Progress:</strong>
                                </div>
                                <p id="tracking-status-text" class="small text-muted mb-0">Initializing tracking stream...</p>
                                <div class="progress mt-2" style="height: 6px; border-radius: 10px; background: var(--ps-border);">
                                    <div id="tracking-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%; border-radius: 10px;"></div>
                                </div>
                            </div>
                            
                            <!-- Assigned Driver Profile -->
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-4" style="background: var(--ps-surface-secondary); border: 1px solid var(--ps-border);">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; border: 1px solid var(--ps-primary);">
                                        <i class="bi bi-person-badge text-primary fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 small text-body">{{ $order->driver->name ?? 'Assigned Courier' }}</h6>
                                        <span class="text-success small d-flex align-items-center gap-1">
                                            <span class="d-inline-block rounded-circle bg-success" style="width: 6px; height: 6px;"></span> On Duty
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="tel:{{ $order->driver->phone ?? '07000000000' }}" class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border: 1px solid var(--ps-border);">
                                        <i class="bi bi-telephone-fill text-primary small"></i>
                                    </a>
                                </div>
                            </div>
                        @else
                            <!-- Delivered Status Message -->
                            <div class="alert alert-success rounded-4 border-0 d-flex align-items-center gap-3 py-3 mb-0">
                                <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                                <div>
                                    <strong class="d-block small text-success">Successfully Delivered!</strong>
                                    <span class="text-muted small">Your package was handed directly to the resident.</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if($order->status === 'delivered' && $order->delivery_proof)
                    <!-- Polaroid Style Proof of Delivery Visual Showcase -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius:20px; overflow:hidden;">
                        <div class="card-body p-4 text-center">
                            <h5 class="fw-bold mb-3 text-start d-flex align-items-center text-body" style="font-family: 'Outfit', sans-serif;">
                                <i class="bi bi-camera-fill text-primary me-2"></i> Delivery Proof
                            </h5>
                            
                            <div class="polaroid-wrapper p-3 bg-white border shadow-sm rounded-3 cursor-pointer transition-all duration-300 hover-up d-inline-block w-100" 
                                 data-call="openProofLightbox" data-args="[&quot;{{ Storage::url($order->delivery_proof) }}&quot;]"
                                 style="border-color: #E2E8F0 !important;">
                                <div class="position-relative overflow-hidden rounded-2" style="aspect-ratio: 4/3; background: #F8FAFC;">
                                    <img src="{{ Storage::url($order->delivery_proof) }}" 
                                         class="w-100 h-100 object-fit-cover transition-all duration-300 hover-scale"
                                         style="transition: transform 0.5s;" 
                                         alt="Proof of Delivery">
                                    <div class="position-absolute top-0 end-0 bg-dark bg-opacity-70 text-white p-2 rounded-bottom-start small d-flex align-items-center gap-1">
                                        <i class="bi bi-zoom-in"></i> Click to Zoom
                                    </div>
                                </div>
                                <div class="pt-3 pb-1 text-center">
                                    <p class="font-signature text-muted mb-0 small" style="font-family: 'Outfit', sans-serif; font-weight: 500;">Delivered on {{ $order->delivered_date ? $order->delivered_date->format('d M Y \a\t H:i') : $order->updated_at->format('d M Y \a\t H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lightbox Modal Container -->
                    <div id="proofLightbox" class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-95 d-none align-items-center justify-content-center" 
                         style="z-index: 9999; backdrop-filter: blur(8px);" data-call="closeProofLightbox">
                        <div class="position-absolute top-0 end-0 p-4">
                            <button type="button" class="btn-close btn-close-white fs-4" aria-label="Close"></button>
                        </div>
                        <div class="p-3 text-center" style="max-width: 90%; max-height: 90%;">
                            <img id="lightboxImage" src="" class="img-fluid rounded-4 shadow-lg border border-white border-2" style="max-height: 80vh; object-fit: contain;">
                            <p class="text-white mt-3 fw-bold font-signature fs-5">Proof of Delivery Confirmation</p>
                        </div>
                    </div>

                    <script nonce="{{ Vite::cspNonce() }}">
                        function openProofLightbox(url) {
                            document.getElementById('lightboxImage').src = url;
                            document.getElementById('proofLightbox').classList.remove('d-none');
                            document.getElementById('proofLightbox').classList.add('d-flex');
                            document.body.style.overflow = 'hidden';
                        }
                        function closeProofLightbox() {
                            document.getElementById('proofLightbox').classList.remove('d-flex');
                            document.getElementById('proofLightbox').classList.add('d-none');
                            document.body.style.overflow = '';
                        }
                    </script>
                @endif
                
                <style>
                    .hover-scale:hover { transform: scale(1.05); }
                    @keyframes pulse-badge {
                        0% { transform: scale(1); opacity: 1; }
                        50% { transform: scale(1.05); opacity: 0.8; }
                        100% { transform: scale(1); opacity: 1; }
                    }
                </style>
                
                <!-- Google Maps delivery route and courier animation -->
                <script nonce="{{ Vite::cspNonce() }}">
                    document.addEventListener('DOMContentLoaded', function () {
                        if (typeof google === 'undefined' || !google.maps) return;

                        // City coordinates lookup
                        const cityCoords = {
                            'leeds': { lat: 53.8008, lng: -1.5491 },
                            'manchester': { lat: 53.4808, lng: -2.2426 },
                            'birmingham': { lat: 52.4862, lng: -1.8904 },
                            'liverpool': { lat: 53.4084, lng: -2.9916 },
                            'bristol': { lat: 51.4545, lng: -2.5879 },
                            'london': { lat: 51.5200, lng: -0.0900 },
                        };

                        const warehouse  = { lat: 51.5074, lng: -0.1278 };
                        const city       = "{{ strtolower($order->shipping_address['city'] ?? '') }}";
                        const hash       = parseInt("{{ $order->id }}") || 10;
                        const destination = cityCoords[city] || {
                            lat: warehouse.lat + 0.025 + (hash % 10) * 0.005,
                            lng: warehouse.lng + 0.025 + (hash % 7) * 0.005,
                        };

                        // SVG pin factory
                        function makePin(color, emoji) {
                            const svg = '<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36">'
                                + '<circle cx="18" cy="18" r="15" fill="' + color + '" stroke="#fff" stroke-width="2"/>'
                                + '<text x="18" y="23" text-anchor="middle" font-size="13">' + emoji + '</text>'
                                + '</svg>';
                            return { url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg), scaledSize: new google.maps.Size(36, 36), anchor: new google.maps.Point(18, 18) };
                        }

                        const map = new google.maps.Map(document.getElementById('live-delivery-map'), {
                            center: warehouse,
                            zoom: 11,
                            zoomControl: false,
                            mapTypeControl: false,
                            streetViewControl: false,
                            fullscreenControl: false,
                        });

                        // Warehouse + destination markers
                        new google.maps.Marker({ position: warehouse,   map: map, icon: makePin('#743089', '🏢'), title: 'Premier Shop Central Depot' });
                        new google.maps.Marker({ position: destination, map: map, icon: makePin('#E17055', '🏠'), title: 'Delivery Address' });

                        // Dashed route line
                        new google.maps.Polyline({
                            path: [warehouse, destination],
                            map: map,
                            geodesic: true,
                            strokeColor: '#3498DB',
                            strokeOpacity: 0,
                            icons: [{ icon: { path: 'M 0,-1 0,1', strokeOpacity: 0.8, scale: 4, strokeColor: '#3498DB' }, offset: '0', repeat: '16px' }],
                        });

                        // Fit bounds
                        var bounds = new google.maps.LatLngBounds();
                        bounds.extend(warehouse);
                        bounds.extend(destination);
                        map.fitBounds(bounds, 40);

                        // Courier animation
                        const status = "{{ $order->status }}";
                        if (status === 'shipped') {
                            const truckMarker = new google.maps.Marker({
                                position: warehouse,
                                map: map,
                                icon: makePin('#3498DB', '🚚'),
                                title: 'Courier',
                                zIndex: 10,
                            });

                            const timeline = [
                                { pct: 0,  msg: "Courier has picked up your package and is departing from the central depot." },
                                { pct: 15, msg: "Courier is checking delivery route constraints." },
                                { pct: 30, msg: "Courier is traveling along the transit highway." },
                                { pct: 50, msg: "Courier is making excellent time; proceeding through main highway corridor." },
                                { pct: 70, msg: "Courier has exited the main route and is entering your local district." },
                                { pct: 85, msg: "Courier is navigating residential streets. Almost there!" },
                                { pct: 95, msg: "Courier is arriving at your address. Please be ready to receive your parcel!" },
                            ];

                            const duration  = 24000;
                            const startTime = performance.now();

                            function animateCourier(ts) {
                                const pct = ((ts - startTime) % duration) / duration;
                                const lat = warehouse.lat + (destination.lat - warehouse.lat) * pct;
                                const lng = warehouse.lng + (destination.lng - warehouse.lng) * pct;
                                truckMarker.setPosition({ lat: lat, lng: lng });
                                map.panTo({ lat: lat, lng: lng });

                                const pctVal = Math.round(pct * 100);
                                const pb     = document.getElementById('tracking-progress-bar');
                                const st     = document.getElementById('tracking-status-text');
                                if (pb) pb.style.width = pctVal + '%';
                                if (st) {
                                    let msg = timeline[0].msg;
                                    for (var i = 0; i < timeline.length; i++) {
                                        if (pctVal >= timeline[i].pct) msg = timeline[i].msg;
                                    }
                                    st.innerText = msg;
                                }
                                requestAnimationFrame(animateCourier);
                            }
                            requestAnimationFrame(animateCourier);

                        } else if (status === 'delivered') {
                            new google.maps.Marker({ position: destination, map: map, icon: makePin('#3498DB', '🚚'), title: 'Delivered' });
                        }
                    });
                </script>
            @endif
            
            <div class="card border-0 shadow-sm mb-4" style="border-radius:20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 d-flex align-items-center">
                        <i class="bi bi-truck text-primary me-2"></i> Order Tracking
                    </h5>
                    
                    <div class="timeline-enhanced mt-3">
                        <div class="timeline-item completed">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <span class="timeline-title">Order Placed</span>
                                <span class="timeline-date">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>

                        <div class="timeline-item {{ $order->processing_date ? 'completed' : ($order->status == 'processing' ? 'active' : '') }}">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <span class="timeline-title {{ $order->processing_date || $order->status == 'processing' ? '' : 'text-muted' }}">Processing</span>
                                @if($order->processing_date)
                                    <span class="timeline-date">{{ $order->processing_date->format('d M Y, H:i') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="timeline-item {{ $order->shipped_date ? 'completed' : ($order->status == 'shipped' ? 'active' : '') }}">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <span class="timeline-title {{ $order->shipped_date || $order->status == 'shipped' ? '' : 'text-muted' }}">Shipped</span>
                                @if($order->shipped_date)
                                    <span class="timeline-date">{{ $order->shipped_date->format('d M Y, H:i') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="timeline-item {{ $order->delivered_date ? 'completed' : ($order->status == 'delivered' ? 'active' : '') }}">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <span class="timeline-title {{ $order->delivered_date || $order->status == 'delivered' ? 'text-success' : 'text-muted' }}">Delivered</span>
                                @if($order->delivered_date)
                                    <span class="timeline-date">{{ $order->delivered_date->format('d M Y, H:i') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($order->status === 'pending')
            <div class="card border-0 shadow-sm bg-danger bg-opacity-10" style="border-radius:20px; border: 1px dashed rgba(220, 53, 69, 0.3) !important;">
                <div class="card-body p-4 text-center">
                    <h6 class="fw-bold text-danger mb-2">Need to change your mind?</h6>
                    <p class="small text-muted mb-3">You can cancel your order while it is still in pending status.</p>
                    <button type="button" class="btn btn-danger w-100 rounded-pill py-2 shadow-sm hover-up" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                        Cancel My Order
                    </button>
                </div>
            </div>

            <!-- Cancel Order Modal -->
            <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
                        <div class="modal-header border-0 p-4 pb-0">
                            <h5 class="modal-title fw-bold">Cancel Order</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('orders.cancel', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="modal-body p-4">
                                <p class="text-muted">Are you sure you want to cancel order <strong>{{ $order->order_number }}</strong>? This action cannot be undone and your items will be returned to stock.</p>
                                <div class="mt-4">
                                    <label class="form-label fw-bold">Reason for cancellation <span class="text-danger">*</span></label>
                                    <textarea name="cancellation_reason" class="form-control border-0 p-3" style="background: var(--ps-surface-secondary); color: var(--ps-text);" rows="4" required placeholder="Please let us know how we can improve..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-0 p-4 pt-0">
                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Keep My Order</button>
                                <button type="submit" class="btn btn-danger rounded-pill px-4 shadow-sm hover-up">Confirm Cancellation</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            @if($order->status === 'cancelled' && $order->cancellation_reason)
            <div class="card border-0 shadow-sm bg-danger bg-opacity-10 border-danger border-start-4" style="border-radius:12px;">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-danger mb-1 small"><i class="bi bi-info-circle me-1"></i> Cancellation Note:</h6>
                    <p class="small mb-0 fst-italic text-muted">"{{ $order->cancellation_reason }}"</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection