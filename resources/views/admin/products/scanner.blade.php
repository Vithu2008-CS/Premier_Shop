{{--
    admin/products/scanner.blade.php — Browser-based QR / barcode scanner
    ========================================================================
    Uses Html5Qrcode v2.3.8 to access the device camera.
    "Start Camera" button triggers getUserMedia (environment camera first, user fallback).
    On successful scan: POST admin.products.findByQr → looks up product → navigates to edit.
    Manual barcode/code input fallback field.
    Camera requires: Permissions-Policy: camera=(self) header (set in SecurityHeadersMiddleware).
--}}
@extends('layouts.admin_noble')
@section('title', 'QR Scanner')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">QR Scanner</li>
  </ol>
</nav>

<div class="row">
    <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-center">Scan Product QR Code</h6>
                <div class="text-center mb-3">
                    <button id="startCamBtn" class="btn btn-primary" onclick="startScanner()">
                        <i data-feather="camera" class="icon-sm mr-1"></i> Start Camera
                    </button>
                </div>
                <div class="position-relative" style="width:100%;max-width:400px;margin:0 auto;border-radius:10px;overflow:hidden;">
                    <div id="qr-reader" style="width:100%;"></div>
                    <div class="laser-scanner-overlay d-none" id="laserOverlay">
                        <div class="laser-line"></div>
                        <div class="scanner-corner corner-tl"></div>
                        <div class="scanner-corner corner-tr"></div>
                        <div class="scanner-corner corner-bl"></div>
                        <div class="scanner-corner corner-br"></div>
                    </div>
                </div>
                <p class="text-muted text-center mt-3">
                    <i data-feather="camera" class="icon-sm mr-1"></i> Point your camera at a product QR code
                </p>
            </div>
        </div>
    </div>

    <div class="col-lg-6 grid-margin stretch-card">
        <div class="card" id="productPanel" style="display:none;">
            <div class="card-body">
                <h6 class="card-title text-success"><i data-feather="check-circle" class="icon-sm mr-2"></i>Product Found</h6>
                <div id="productInfo">
                    <div class="text-center mb-4">
                        <img id="productImage" src="" style="max-height:150px;border-radius:10px;border:1px solid #e8ebf1;" alt="">
                    </div>
                    <h4 id="productName" class="font-weight-bold mb-3"></h4>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Price</span>
                        <strong id="productPrice"></strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom mb-4">
                        <span class="text-muted">Current Stock</span>
                        <strong id="currentStock" class="text-primary tx-20"></strong>
                    </div>

                    <div class="mt-4">
                        <h6 class="card-title">Update Stock Quantity</h6>
                        <div class="input-group mb-3">
                            <input type="number" id="newStock" class="form-control" min="0">
                            <div class="input-group-append">
                                <button id="updateStockBtn" class="btn btn-primary" onclick="updateStock()">
                                    <i data-feather="save" class="icon-sm mr-1"></i> Update
                                </button>
                            </div>
                        </div>
                        <div id="updateResult" class="mt-3" style="display:none;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card grid-margin stretch-card text-center" id="noProductPanel">
            <div class="card-body py-5">
                <i data-feather="maximize" class="icon-xxl text-muted mb-4"></i>
                <h5 class="text-muted">Scan a QR code to see details</h5>
                <p class="text-muted small">You can then update the stock quantity directly.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .laser-scanner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 10;
        background: rgba(0, 255, 0, 0.03);
        border: 2px solid rgba(0, 255, 0, 0.2) !important;
        border-radius: 10px;
    }
    .laser-line {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, rgba(0,255,0,0) 0%, rgba(0,255,0,1) 50%, rgba(0,255,0,0) 100%);
        box-shadow: 0 0 15px rgba(0, 255, 0, 0.9), 0 0 5px rgba(0, 255, 0, 0.6);
        animation: laserSweep 2s cubic-bezier(0.4, 0, 0.2, 1) infinite alternate;
    }
    @keyframes laserSweep {
        0% { top: 0%; }
        100% { top: 100%; }
    }
    .scanner-corner {
        position: absolute;
        width: 24px;
        height: 24px;
        border-color: #00FF00;
        border-style: solid;
        border-width: 0;
        filter: drop-shadow(0 0 4px rgba(0,255,0,0.5));
    }
    .corner-tl { top: 15px; left: 15px; border-top-width: 4px; border-left-width: 4px; }
    .corner-tr { top: 15px; right: 15px; border-top-width: 4px; border-right-width: 4px; }
    .corner-bl { bottom: 15px; left: 15px; border-bottom-width: 4px; border-left-width: 4px; }
    .corner-br { bottom: 15px; right: 15px; border-bottom-width: 4px; border-right-width: 4px; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let currentProductId = null;
    let html5QrCode = null;

    function playSynthBeep() {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc1 = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();
            
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(880, audioCtx.currentTime); // A5 note
            
            const filter = audioCtx.createBiquadFilter();
            filter.type = 'highpass';
            filter.frequency.setValueAtTime(400, audioCtx.currentTime);
            
            gainNode.gain.setValueAtTime(0, audioCtx.currentTime);
            gainNode.gain.linearRampToValueAtTime(0.15, audioCtx.currentTime + 0.02);
            gainNode.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 0.15);
            
            osc1.connect(filter);
            filter.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            
            osc1.start();
            osc1.stop(audioCtx.currentTime + 0.16);
            
            setTimeout(() => {
                const osc2 = audioCtx.createOscillator();
                const gainNode2 = audioCtx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(1320, audioCtx.currentTime); // E6 note
                
                gainNode2.gain.setValueAtTime(0, audioCtx.currentTime);
                gainNode2.gain.linearRampToValueAtTime(0.08, audioCtx.currentTime + 0.02);
                gainNode2.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 0.1);
                
                osc2.connect(gainNode2);
                gainNode2.connect(audioCtx.destination);
                
                osc2.start();
                osc2.stop(audioCtx.currentTime + 0.12);
            }, 40);
            
        } catch (e) {
            console.warn("Audio Context beep failed", e);
        }
    }

    function startScanner() {
        document.getElementById('startCamBtn').style.display = 'none';
        document.getElementById('qr-reader').innerHTML = '';

        // Recreate fresh instance every attempt — avoids stale internal state
        html5QrCode = new Html5Qrcode("qr-reader");

        const onScan = (decodedText) => {
            const match = decodedText.match(/products\/(\d+)\/qr-lookup/);
            if (match) {
                playSynthBeep();
                fetchProduct(match[1]);
            }
        };

        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        const handleStartSuccess = () => {
            const overlay = document.getElementById('laserOverlay');
            if (overlay) overlay.classList.remove('d-none');
        };

        // Try environment (mobile back cam), fallback to user (desktop webcam), fallback to any
        html5QrCode.start({ facingMode: "environment" }, config, onScan, () => {})
            .then(handleStartSuccess)
            .catch(() => {
                html5QrCode = new Html5Qrcode("qr-reader");
                return html5QrCode.start({ facingMode: "user" }, config, onScan, () => {})
                    .then(handleStartSuccess);
            })
            .catch(err => {
                showScannerError(err);
            });
    }

    function showScannerError(err) {
        console.error("Camera error:", err);
        let errorMsg = "Camera access denied or not available.";

        if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
            errorMsg = "<strong>Security Error:</strong> Camera requires HTTPS. Open this page over HTTPS.";
        } else if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
            errorMsg = "<strong>Permission Denied:</strong> Allow camera in browser address bar, then click Retry.";
        } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
            errorMsg = "<strong>No Camera Found:</strong> No webcam detected on this device.";
        } else if (err.name === 'NotReadableError') {
            errorMsg = "<strong>Camera Busy:</strong> Camera is in use by another app. Close it and click Retry.";
        } else {
            errorMsg = `<strong>Camera Error:</strong> ${err.message || err}`;
        }

        document.getElementById('qr-reader').innerHTML = `
            <div class="alert alert-warning p-3 text-center" style="border-radius:10px; font-size:0.9rem;">
                <div class="d-flex flex-column gap-2 align-items-center">
                    <i class="bi bi-exclamation-triangle fs-4"></i>
                    <span>${errorMsg}</span>
                </div>
            </div>
        `;
        document.getElementById('startCamBtn').style.display = 'inline-block';
        document.getElementById('startCamBtn').textContent = 'Retry Camera';
    }

    function fetchProduct(productId) {
        currentProductId = productId;
        fetch('{{ route("admin.products.findByQr") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) { alert('Product not found'); return; }
            document.getElementById('productName').textContent = data.name;
            document.getElementById('productPrice').textContent = '£' + parseFloat(data.price).toFixed(2);
            document.getElementById('currentStock').textContent = data.stock;
            document.getElementById('newStock').value = data.stock;
            document.getElementById('productImage').src = data.image;
            document.getElementById('productPanel').style.display = 'block';
            document.getElementById('noProductPanel').style.display = 'none';
            document.getElementById('updateResult').style.display = 'none';
        });
    }

    function updateStock() {
        const newStock = document.getElementById('newStock').value;
        fetch(`/admin/products/${currentProductId}/update-stock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ stock: parseInt(newStock) })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('currentStock').textContent = data.new_stock;
                const result = document.getElementById('updateResult');
                result.style.display = 'block';
                result.innerHTML = '<div class="alert alert-success" style="border-radius:10px;">Stock updated to ' + data.new_stock + '!</div>';
            }
        });
    }
</script>
@endpush

