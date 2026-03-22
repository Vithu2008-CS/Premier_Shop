@extends('layouts.admin_noble')
@section('title', 'Customers')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active" aria-current="page">Customers</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h6 class="card-title mb-0">Customer Management</h6>
            <div class="d-flex align-items-center">
                <span class="badge badge-light-primary mr-2">{{ $customers->total() }} total</span>
            </div>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Customer</th>
                <th>Email</th>
                <th>Role</th>
                <th>Phone</th>
                <th>Age</th>
                <th>Orders</th>
                <th>Joined</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($customers as $customer)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                        <div class="wd-35 h-35 rounded-circle bg-light-success d-flex align-items-center justify-content-center mr-2 text-success font-weight-bold" style="font-size: 0.8rem;">
                            {{ substr($customer->name, 0, 1) }}
                        </div>
                        <div>
                            <span class="font-weight-bold d-block">{{ $customer->name }}</span>
                            @if($customer->isUnder16()) 
                                <span class="badge badge-warning py-0" style="font-size: 0.6rem;">UNDER 16</span>
                            @endif
                        </div>
                    </div>
                  </td>
                  <td class="text-muted small">{{ $customer->email }}</td>
                  <td>
                    <span class="badge badge-outline-primary">{{ $customer->role?->display_name ?? 'User' }}</span>
                  </td>
                  <td class="small">{{ $customer->phone ?? '—' }}</td>
                  <td>
                    @if($customer->dob)
                        <span class="font-weight-bold">{{ $customer->age }}</span> <small class="text-muted">yrs</small>
                    @else
                        <span class="text-muted small">—</span>
                    @endif
                  </td>
                  <td>
                    <span class="badge badge-{{ $customer->orders_count > 0 ? 'success' : 'light' }}">
                        {{ $customer->orders_count }}
                    </span>
                  </td>
                  <td class="text-muted small">{{ $customer->created_at->format('d M Y') }}</td>
                  <td class="text-right">
                    <div class="dropdown">
                        <button class="btn btn-link p-0" type="button" id="dropCust-{{ $customer->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropCust-{{ $customer->id }}">
                          <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.customers.show', $customer) }}">
                              <i data-feather="eye" class="icon-sm mr-2"></i> View Profile
                          </a>
                          <div class="dropdown-divider"></div>
                          <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Truly delete this customer?')">
                              @csrf @method('DELETE')
                              <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                                  <i data-feather="trash-2" class="icon-sm mr-2"></i> Delete
                              </button>
                          </form>
                        </div>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <i data-feather="users" class="icon-xxl text-muted mb-3"></i>
                        <p class="text-muted">No customers registered yet.</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-4">
            {{ $customers->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
