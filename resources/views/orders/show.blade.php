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
                                 class="shadow-sm" onerror="this.onerror=null; this.src='/images/placeholder-product.png'">
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
                <!-- Leaflet CSS & JS CDNs -->
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
                
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
                                 onclick="openProofLightbox('{{ Storage::url($order->delivery_proof) }}')"
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
                         style="z-index: 9999; backdrop-filter: blur(8px);" onclick="closeProofLightbox()">
                        <div class="position-absolute top-0 end-0 p-4">
                            <button type="button" class="btn-close btn-close-white fs-4" aria-label="Close"></button>
                        </div>
                        <div class="p-3 text-center" style="max-width: 90%; max-height: 90%;">
                            <img id="lightboxImage" src="" class="img-fluid rounded-4 shadow-lg border border-white border-2" style="max-height: 80vh; object-fit: contain;">
                            <p class="text-white mt-3 fw-bold font-signature fs-5">Proof of Delivery Confirmation</p>
                        </div>
                    </div>

                    <script>
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
                
                <!-- Live Tracking Simulation and Map Rendering Script -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // 1. Define Warehouses (Origin) and Dest Coordinates based on active city
                        const warehouse = [51.5074, -0.1278]; // London Depot
                        let destination = [51.5300, -0.0800];  // Default Offset London
                        
                        const customerCity = "{{ strtolower($order->shipping_address['city'] ?? '') }}";
                        
                        // Map coordinates index for UK cities
                        const cityCoords = {
                            'leeds': [53.8008, -1.5491],
                            'manchester': [53.4808, -2.2426],
                            'birmingham': [52.4862, -1.8904],
                            'liverpool': [53.4084, -2.9916],
                            'bristol': [51.4545, -2.5879],
                            'london': [51.5200, -0.0900]
                        };
                        
                        if (cityCoords[customerCity]) {
                            destination = cityCoords[customerCity];
                        } else {
                            // If arbitrary city, generate custom route near central depot based on order ID hash
                            const hash = parseInt("{{ $order->id }}") || 10;
                            const offsetLat = 0.025 + (hash % 10) * 0.005;
                            const offsetLng = 0.025 + (hash % 7) * 0.005;
                            destination = [warehouse[0] + offsetLat, warehouse[1] + offsetLng];
                        }
                        
                        // 2. Initialize Leaflet Map
                        const map = L.map('live-delivery-map', {
                            zoomControl: false,
                            attributionControl: false
                        }).setView(warehouse, 11);
                        
                        // Use a beautiful, clean CartoDB Voyager map tile to match premium aesthetics
                        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                            maxZoom: 19
                        }).addTo(map);
                        
                        // 3. Create Custom Markers
                        const warehouseIcon = L.divIcon({
                            html: '<div style="background-color: #6C5CE7; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; border: 2px solid white; box-shadow: 0 4px 6px rgba(0,0,0,0.15);"><i class="bi bi-building"></i></div>',
                            className: '',
                            iconSize: [32, 32],
                            iconAnchor: [16, 16]
                        });
                        
                        const destinationIcon = L.divIcon({
                            html: '<div style="background-color: #E17055; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; border: 2px solid white; box-shadow: 0 4px 6px rgba(0,0,0,0.15);"><i class="bi bi-house-door-fill"></i></div>',
                            className: '',
                            iconSize: [32, 32],
                            iconAnchor: [16, 16]
                        });
                        
                        const truckIcon = L.divIcon({
                            html: '<div style="background-color: #3498DB; color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; border: 2px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.25);"><i class="bi bi-truck"></i></div>',
                            className: '',
                            iconSize: [36, 36],
                            iconAnchor: [18, 18]
                        });
                        
                        L.marker(warehouse, { icon: warehouseIcon }).addTo(map).bindPopup("Premier Shop Central Depot");
                        L.marker(destination, { icon: destinationIcon }).addTo(map).bindPopup("Delivery Address");
                        
                        // 4. Plot Delivery Route Line
                        const routeLine = L.polyline([warehouse, destination], {
                            color: '#3498DB',
                            weight: 4,
                            opacity: 0.8,
                            dashArray: '8, 8'
                        }).addTo(map);
                        
                        // Fit map bounds to show route clearly
                        map.fitBounds(routeLine.getBounds(), { padding: [30, 30] });
                        
                        // 5. Courier Animation Logic (Shipped Status only)
                        const status = "{{ $order->status }}";
                        if (status === 'shipped') {
                            const truckMarker = L.marker(warehouse, { icon: truckIcon }).addTo(map);
                            
                            const statusTimeline = [
                                { pct: 0, msg: "Courier has picked up your package and is departing from the central depot." },
                                { pct: 15, msg: "Courier is checking delivery route constraints." },
                                { pct: 30, msg: "Courier is traveling along the transit highway." },
                                { pct: 50, msg: "Courier is making excellent time; proceeding through main highway corridor." },
                                { pct: 70, msg: "Courier has exited the main route and is entering your local district." },
                                { pct: 85, msg: "Courier is navigating residential streets. Almost there!" },
                                { pct: 95, msg: "Courier is arriving at your address. Please be ready to receive your parcel!" }
                            ];
                            
                            let progress = 0;
                            const duration = 24000; // 24 seconds loop
                            const startTime = performance.now();
                            
                            function animateCourier(timestamp) {
                                const elapsed = (timestamp - startTime) % duration;
                                progress = elapsed / duration;
                                
                                // Calculate position coordinate along linear path
                                const currentLat = warehouse[0] + (destination[0] - warehouse[0]) * progress;
                                const currentLng = warehouse[1] + (destination[1] - warehouse[1]) * progress;
                                const currentPos = [currentLat, currentLng];
                                
                                truckMarker.setLatLng(currentPos);
                                
                                // Dynamic map centering to follow truck
                                map.panTo(currentPos, { animate: true, duration: 0.2 });
                                
                                // Update progress bar and text logs
                                const pctVal = Math.round(progress * 100);
                                const progressBar = document.getElementById('tracking-progress-bar');
                                if (progressBar) progressBar.style.width = pctVal + '%';
                                
                                // Find correct message matching progress
                                let activeMsg = statusTimeline[0].msg;
                                for (let i = 0; i < statusTimeline.length; i++) {
                                    if (pctVal >= statusTimeline[i].pct) {
                                        activeMsg = statusTimeline[i].msg;
                                    }
                                }
                                const statusText = document.getElementById('tracking-status-text');
                                if (statusText) statusText.innerText = activeMsg;
                                
                                requestAnimationFrame(animateCourier);
                            }
                            
                            requestAnimationFrame(animateCourier);
                        } else if (status === 'delivered') {
                            // If delivered, position the truck stationary at the destination
                            L.marker(destination, { icon: truckIcon }).addTo(map);
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