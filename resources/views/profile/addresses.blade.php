@extends('layouts.app')

@section('title', 'My Addresses - Premier Shop')

@section('content')
<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('profile.edit') }}" class="text-decoration-none">Profile</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Addresses</li>
                </ol>
            </nav>
            <h2 class="mb-0 fw-bold">My Addresses</h2>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addressModal" onclick="resetForm()">
                <i class="bi bi-plus-circle me-1"></i> Add New Address
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($addresses->isEmpty())
        <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
            <div class="mb-3">
                <i class="bi bi-geo-alt text-muted" style="font-size: 3rem;"></i>
            </div>
            <h5>No addresses saved yet</h5>
            <p class="text-muted mb-4">Save your delivery addresses for a faster checkout experience.</p>
            <button class="btn btn-outline-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addressModal" onclick="resetForm()">
                Add an Address
            </button>
        </div>
    @else
        <div class="row g-4">
            @foreach($addresses as $address)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 position-relative {{ $address->is_default ? 'border-primary border-2 border' : '' }}">
                        @if($address->is_default)
                            <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-primary" style="z-index: 1;">
                                Default
                            </span>
                        @endif
                        <div class="card-body p-4">
                            <h6 class="card-title fw-bold d-flex align-items-center gap-2 mb-3">
                                <i class="bi bi-tag text-muted"></i> {{ $address->label }}
                            </h6>
                            <p class="card-text mb-1"><i class="bi bi-geo-alt text-muted me-2"></i>{{ $address->address_line }}</p>
                            <p class="card-text mb-1 ps-4 text-muted">{{ $address->city }}</p>
                            @if($address->postcode)
                            <p class="card-text mb-3 ps-4 text-muted">{{ $address->postcode }}</p>
                            @endif
                            <p class="card-text mb-0"><i class="bi bi-telephone text-muted me-2"></i>{{ $address->phone }}</p>
                        </div>
                        <div class="card-footer bg-transparent border-top p-3 d-flex justify-content-between align-items-center">
                            @if(!$address->is_default)
                                <form action="{{ route('addresses.setDefault', $address) }}" method="POST" class="m-0">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-link text-decoration-none p-0">Set as Default</button>
                                </form>
                            @else
                                <span class="text-muted small"><i class="bi bi-check2"></i> Default Address</span>
                            @endif
                            
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-light rounded-circle" onclick='editAddress(@json($address))' data-bs-toggle="modal" data-bs-target="#addressModal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this address?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Address Modal -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="addressModalLabel">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addressForm" action="{{ route('addresses.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">LABEL</label>
                        <input type="text" class="form-control form-control-lg rounded-3" name="label" id="label" placeholder="e.g. Home, Work" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">STREET ADDRESS</label>
                        <input type="text" class="form-control form-control-lg rounded-3" name="address_line" id="address_line" required>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">CITY / TOWN</label>
                            <input type="text" class="form-control form-control-lg rounded-3" name="city" id="city" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">POSTCODE</label>
                            <input type="text" class="form-control form-control-lg rounded-3" name="postcode" id="postcode">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">PHONE NUMBER</label>
                        <input type="text" class="form-control form-control-lg rounded-3" name="phone" id="phone" required>
                    </div>
                    
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_default" id="is_default" value="1">
                        <label class="form-check-label" for="is_default">Set as my default delivery address</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold" id="submitBtn">
                        Save Address
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function resetForm() {
        document.getElementById('addressForm').action = "{{ route('addresses.store') }}";
        document.getElementById('formMethod').value = "POST";
        document.getElementById('addressModalLabel').innerText = "Add New Address";
        document.getElementById('submitBtn').innerText = "Save Address";
        
        document.getElementById('label').value = "Home";
        document.getElementById('address_line').value = "";
        document.getElementById('city').value = "";
        document.getElementById('postcode').value = "";
        document.getElementById('phone').value = "{{ auth()->user()->phone }}";
        document.getElementById('is_default').checked = false;
    }

    function editAddress(address) {
        document.getElementById('addressForm').action = "/addresses/" + address.id;
        document.getElementById('formMethod').value = "PUT";
        document.getElementById('addressModalLabel').innerText = "Edit Address";
        document.getElementById('submitBtn').innerText = "Update Address";
        
        document.getElementById('label').value = address.label;
        document.getElementById('address_line').value = address.address_line;
        document.getElementById('city').value = address.city;
        document.getElementById('postcode').value = address.postcode || "";
        document.getElementById('phone').value = address.phone;
        document.getElementById('is_default').checked = address.is_default;
    }
</script>
@endsection
