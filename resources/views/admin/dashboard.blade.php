@extends('layouts.admin_noble')
@section('title', 'Dashboard')

@push('plugin-styles')
  <link rel="stylesheet" href="{{ asset('admin_assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Welcome to Dashboard</h4>
  </div>
  <div class="d-flex align-items-center flex-wrap text-nowrap">
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-icon-text mb-2 mb-md-0">
      <i class="btn-icon-prepend" data-feather="plus-circle"></i>
      Add Product
    </a>
  </div>
</div>

<div class="row">
  <div class="col-12 col-xl-12 stretch-card">
    <div class="row flex-grow">
      {{-- Total Products --}}
      <div class="col-md-3 grid-margin stretch-card">
        <a href="{{ route('admin.products.index') }}" class="text-decoration-none flex-grow-1">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-baseline">
                <h6 class="card-title mb-0">Total Products</h6>
              </div>
              <div class="row">
                <div class="col-12 col-md-12 col-xl-12">
                  <h3 class="mb-2 mt-2">{{ $stats['totalProducts'] }}</h3>
                  <div class="d-flex align-items-baseline">
                    <p class="text-primary">
                      <i data-feather="package" class="icon-sm mb-1"></i>
                      <span class="ml-1">Active inventory</span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
      {{-- Total Orders --}}
      <div class="col-md-3 grid-margin stretch-card">
        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none flex-grow-1">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-baseline">
                <h6 class="card-title mb-0">Total Orders</h6>
              </div>
              <div class="row">
                <div class="col-12 col-md-12 col-xl-12">
                  <h3 class="mb-2 mt-2">{{ $stats['totalOrders'] }}</h3>
                  <div class="d-flex align-items-baseline">
                    <p class="text-success">
                      <i data-feather="shopping-cart" class="icon-sm mb-1"></i>
                      <span class="ml-1">Lifetime sales</span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
      {{-- Total Revenue --}}
      <div class="col-md-3 grid-margin stretch-card">
        <a href="{{ route('admin.reports.index') }}" class="text-decoration-none flex-grow-1">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-baseline">
                <h6 class="card-title mb-0">Total Revenue</h6>
              </div>
              <div class="row">
                <div class="col-12 col-md-12 col-xl-12">
                  <h3 class="mb-2 mt-2">£{{ number_format($stats['totalRevenue'], 0) }}</h3>
                  <div class="d-flex align-items-baseline">
                    <p class="text-info">
                      <i data-feather="credit-card" class="icon-sm mb-1"></i>
                      <span class="ml-1">Gross earnings</span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
      {{-- Total Customers --}}
      <div class="col-md-3 grid-margin stretch-card">
        <a href="{{ route('admin.customers.index') }}" class="text-decoration-none flex-grow-1">
          <div class="card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-baseline">
                <h6 class="card-title mb-0">Total Customers</h6>
              </div>
              <div class="row">
                <div class="col-12 col-md-12 col-xl-12">
                  <h3 class="mb-2 mt-2">{{ $stats['totalCustomers'] }}</h3>
                  <div class="d-flex align-items-baseline">
                    <p class="text-warning">
                      <i data-feather="users" class="icon-sm mb-1"></i>
                      <span class="ml-1">Registered users</span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>
</div>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Weekly Sales Performance</h6>
                <div id="apexChartSales" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

@if($stats['lowStock'] > 0)
    <div class="alert alert-fill-warning mb-4" role="alert">
        <i data-feather="alert-triangle" class="icon-md mr-2"></i>
        <strong>Attention!</strong> {{ $stats['lowStock'] }} products have low stock (< 10 units).
    </div>
@endif

<div class="row">
  <div class="col-lg-8 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-baseline mb-2">
            <h6 class="card-title mb-0">Recent Orders</h6>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-link">View All</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th class="pt-0">Order #</th>
                <th class="pt-0">Customer</th>
                <th class="pt-0">Total</th>
                <th class="pt-0">Status</th>
                <th class="pt-0">Date</th>
              </tr>
            </thead>
            <tbody>
              @forelse($stats['recentOrders'] as $order)
                <tr>
                  <td>
                      <a href="{{ route('admin.orders.show', $order) }}" class="font-weight-bold text-primary">
                          {{ $order->order_number }}
                      </a>
                  </td>
                  <td>{{ $order->user->name }}</td>
                  <td>£{{ number_format($order->total, 2) }}</td>
                  <td>
                    @php
                        $statusColors = [
                            'pending'=>'warning',
                            'processing'=>'info',
                            'shipped'=>'primary',
                            'delivered'=>'success',
                            'cancelled'=>'danger'
                        ];
                    @endphp
                    <span class="badge badge-{{ $statusColors[$order->status] ?? 'secondary' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                  </td>
                  <td class="text-muted">{{ $order->created_at->diffForHumans() }}</td>
                </tr>
              @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        No orders yet
                    </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div> 
    </div>
  </div>
  <div class="col-lg-4 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Quick Actions</h6>
        <div class="d-flex flex-column">
            <a href="{{ route('admin.products.create') }}" class="btn btn-outline-primary btn-block mb-2 text-left">
                <i data-feather="plus" class="icon-sm mr-2"></i> Add Product
            </a>
            <a href="{{ route('admin.scanner') }}" class="btn btn-outline-primary btn-block mb-2 text-left">
                <i data-feather="maximize" class="icon-sm mr-2"></i> Scan QR Code
            </a>
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-outline-primary btn-block mb-2 text-left">
                <i data-feather="tag" class="icon-sm mr-2"></i> Create Coupon
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary btn-block text-left">
                <i data-feather="shopping-cart" class="icon-sm mr-2"></i> Manage Orders
            </a>
        </div>
        <hr>
        <h6 class="card-title">Store Info</h6>
        <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted">Active Products</span>
            <span class="font-weight-bold">{{ \App\Models\Product::where('is_active', true)->count() }}</span>
        </div>
        <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted">Categories</span>
            <span class="font-weight-bold">{{ \App\Models\Category::count() }}</span>
        </div>
        <div class="d-flex justify-content-between py-2">
            <span class="text-muted">Low Stock Items</span>
            <span class="font-weight-bold text-warning">{{ $stats['lowStock'] }}</span>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('plugin-scripts')
  <script src="{{ asset('admin_assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
  <script src="{{ asset('admin_assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
@endpush

@push('scripts')
  <script src="{{ asset('admin_assets/js/datepicker.js') }}"></script>
  <script>
    $(function() {
      'use strict';
      
      if ($('#apexChartSales').length) {
        var options = {
          chart: {
            type: "area",
            height: 300,
            foreColor: "#94a2b3",
            toolbar: {
              show: false
            },
            sparkline: {
              enabled: false
            },
          },
          series: [{
            name: 'Total Revenue',
            data: @json($salesData)
          }],
          stroke: {
            width: 3,
            curve: 'smooth'
          },
          fill: {
            type: 'gradient',
            gradient: {
              shadeIntensity: 1,
              opacityFrom: 0.7,
              opacityTo: 0.3,
              stops: [0, 90, 100]
            }
          },
          xaxis: {
            categories: @json($salesLabels),
            axisBorder: {
              show: false
            },
            axisTicks: {
              show: false
            },
          },
          yaxis: {
            labels: {
                formatter: function (value) {
                    return "£" + value.toFixed(0);
                }
            }
          },
          title: {
            text: 'Revenue (Last 7 Days)',
            align: 'left',
            style: {
              fontSize: '14px',
              color: '#6e7985'
            }
          },
          markers: {
            size: 5,
            strokeWidth: 3,
            hover: {
              size: 7
            }
          },
          colors: ["#727cf5"],
          grid: {
            padding: {
              left: 0,
              right: 0
            }
          }
        };

        var chart = new ApexCharts(document.querySelector("#apexChartSales"), options);
        chart.render();
      }
    });
  </script>
@endpush

