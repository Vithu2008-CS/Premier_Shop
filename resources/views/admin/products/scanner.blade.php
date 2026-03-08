@extends('layouts.admin')
@section('title', 'QR Scanner — Admin')

@section('content')
<div class="admin-topbar">
    <div>
        <h2><i class="bi bi-qr-code-scan me-2"></i>QR Code Scanner</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">QR Scanner</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="admin-card text-center">
            <div class="card-title"><i class="bi bi-camera me-2"></i>Scan Product QR Code</div>
            <div id="qr-reader" style="width:100%;max-width:400px;margin:0 auto;border-radius:14px;overflow:hidden;"></div>
            <p style="color:var(--admin-muted);margin-top:12px;"><i class="bi bi-camera me-1"></i>Point your camera at a product QR code</p>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="admin-card" id="productPanel" style="display:none;">
            <div class="card-title"><i class="bi bi-check-circle text-success me-2"></i>Product Found</div>
            <div id="productInfo">
                <div class="text-center mb-3">
                    <img id="productImage" src="" style="max-height:150px;border-radius:12px;" alt="">
                </div>
                <h4 id="productName" class="fw-bold mb-2"></h4>
                <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--admin-border);">
                    <span style="color:var(--admin-muted);">Price</span>
                    <strong id="productPrice"></strong>
                </div>
                <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--admin-border);">
                    <span style="color:var(--admin-muted);">Current Stock</span>
                    <strong id="currentStock" style="color:#A29BFE;font-size:1.2rem;"></strong>
                </div>

                <div class="mt-4">
                    <div class="card-title"><i class="bi bi-arrow-repeat me-2"></i>Update Stock Quantity</div>
                    <div class="d-flex gap-3 align-items-end">
                        <div class="flex-grow-1">
                            <label class="form-label">New Stock Level</label>
                            <input type="number" id="newStock" class="form-control form-control-lg" min="0" style="background:rgba(255,255,255,0.05);border-color:var(--admin-border);color:#fff;">
                        </div>
                        <button id="updateStockBtn" class="btn btn-admin btn-lg" onclick="updateStock()">
                            <i class="bi bi-save me-1"></i> Update
                        </button>
                    </div>
                    <div id="updateResult" class="mt-3" style="display:none;"></div>
                </div>
            </div>
        </div>

        <div class="admin-card text-center" id="noProductPanel">
            <div class="py-5">
                <i class="bi bi-qr-code" style="font-size:4rem;color:var(--admin-muted);display:block;margin-bottom:16px;"></i>
                <h5 style="color:var(--admin-muted);">Scan a QR code to see product details</h5>
                <p style="color:var(--admin-muted);font-size:0.9rem;">You can then update the stock quantity directly.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let currentProductId = null;

    const html5QrCode = new Html5Qrcode("qr-reader");
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        (decodedText) => {
            const match = decodedText.match(/products\/(\d+)\/qr-lookup/);
            if (match) {
                fetchProduct(match[1]);
            }
        },
        (error) => { /* ignore scan errors */ }
    ).catch(err => {
        document.getElementById('qr-reader').innerHTML = '<div class="alert alert-warning" style="border-radius:10px;"><i class="bi bi-camera-video-off me-2"></i>Camera access denied or not available.</div>';
    });

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
                result.innerHTML = '<div class="alert alert-success" style="border-radius:10px;"><i class="bi bi-check-circle me-1"></i> Stock updated to ' + data.new_stock + '!</div>';
            }
        });
    }
</script>
@endpush
