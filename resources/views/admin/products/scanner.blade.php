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
                <div id="qr-reader" style="width:100%;max-width:400px;margin:0 auto;border-radius:10px;overflow:hidden;"></div>
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
        document.getElementById('qr-reader').innerHTML = '<div class="alert alert-warning" style="border-radius:10px;">Camera access denied or not available.</div>';
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
                result.innerHTML = '<div class="alert alert-success" style="border-radius:10px;">Stock updated to ' + data.new_stock + '!</div>';
            }
        });
    }
</script>
@endpush

