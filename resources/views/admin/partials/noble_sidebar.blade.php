{{--
    admin/partials/noble_sidebar.blade.php — Admin left sidebar navigation
    =========================================================================
    Noble UI sidebar: brand logo, nav links grouped by section (Dashboard, Products,
    Orders, Customers, Drivers, Reports, Mail, Settings, Roles).
    Active link state via Route::is() comparisons.
    Permission-gated links hidden based on auth()->user()->hasPermission().
    Included by layouts/admin_noble.blade.php.
--}}
<nav class="sidebar">
  <div class="sidebar-header">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
      Premier<span>Shop</span>
    </a>
    <div class="sidebar-toggler not-active">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>
  <div class="sidebar-body">
    <ul class="nav">
      <li class="nav-item nav-category">Main</li>
      <li class="nav-item {{ Request::is('admin/dashboard') ? 'active' : '' }}">
        <a href="{{ route('admin.dashboard') }}" class="nav-link">
          <i class="link-icon" data-feather="box"></i>
          <span class="link-title">Dashboard</span>
        </a>
      </li>
      
      @if(auth()->user()->hasPermission('mail.view'))
      <li class="nav-item nav-category">Web Apps</li>
      <li class="nav-item {{ Request::is('admin/mail*') ? 'active' : '' }}">
        <a href="{{ route('admin.mail.inbox') }}" class="nav-link">
          <i class="link-icon" data-feather="mail"></i>
          <span class="link-title">Email</span>
        </a>
      </li>
      @endif
      
      <li class="nav-item nav-category">Shop Management</li>
      
      @if(auth()->user()->hasPermission('categories.view'))
      <li class="nav-item {{ Request::is('admin/categories*') ? 'active' : '' }}">
        <a href="{{ route('admin.categories.index') }}" class="nav-link">
          <i class="link-icon" data-feather="layers"></i>
          <span class="link-title">Categories</span>
        </a>
      </li>
      @endif

      @if(auth()->user()->hasPermission('products.view'))
      <li class="nav-item {{ Request::is('admin/products*') ? 'active' : '' }}">
        <a href="{{ route('admin.products.index') }}" class="nav-link">
          <i class="link-icon" data-feather="shopping-bag"></i>
          <span class="link-title">Products</span>
        </a>
      </li>
      @endif

      @if(auth()->user()->hasPermission('orders.view'))
      <li class="nav-item {{ Request::is('admin/orders*') ? 'active' : '' }}">
        <a href="{{ route('admin.orders.index') }}" class="nav-link">
          <i class="link-icon" data-feather="shopping-cart"></i>
          <span class="link-title">Orders</span>
        </a>
      </li>
      @endif

      @if(auth()->user()->hasPermission('returns.view'))
      <li class="nav-item {{ Request::is('admin/returns*') ? 'active' : '' }}">
        <a href="{{ route('admin.returns.index') }}" class="nav-link">
          <i class="link-icon" data-feather="corner-down-left"></i>
          <span class="link-title">Returns</span>
        </a>
      </li>
      @endif

      @if(auth()->user()->hasPermission('reviews.view'))
      <li class="nav-item {{ Request::is('admin/reviews*') ? 'active' : '' }}">
        <a href="{{ route('admin.reviews.index') }}" class="nav-link">
          <i class="link-icon" data-feather="star"></i>
          <span class="link-title">Reviews</span>
        </a>
      </li>
      @endif

      @if(auth()->user()->hasPermission('customers.view'))
      <li class="nav-item {{ Request::is('admin/customers*') ? 'active' : '' }}">
        <a href="{{ route('admin.customers.index') }}" class="nav-link">
          <i class="link-icon" data-feather="users"></i>
          <span class="link-title">Customers</span>
        </a>
      </li>
      @endif

      @if(auth()->user()->hasPermission('drivers.view'))
      <li class="nav-item {{ Request::is('admin/drivers*') ? 'active' : '' }}">
        <a href="{{ route('admin.drivers.index') }}" class="nav-link">
          <i class="link-icon" data-feather="truck"></i>
          <span class="link-title">Drivers</span>
        </a>
      </li>
      @endif

      @if(auth()->user()->hasPermission('coupons.view') || auth()->user()->hasPermission('sliders.view'))
      <li class="nav-item nav-category">Promotions</li>
      @if(auth()->user()->hasPermission('coupons.view'))
      <li class="nav-item {{ Request::is('admin/coupons*') ? 'active' : '' }}">
        <a href="{{ route('admin.coupons.index') }}" class="nav-link">
          <i class="link-icon" data-feather="tag"></i>
          <span class="link-title">Coupons</span>
        </a>
      </li>
      @endif
      @if(auth()->user()->hasPermission('sliders.view'))
      <li class="nav-item {{ Request::is('admin/sliders*') ? 'active' : '' }}">
        <a href="{{ route('admin.sliders.index') }}" class="nav-link">
          <i class="link-icon" data-feather="image"></i>
          <span class="link-title">Home Sliders</span>
        </a>
      </li>
      @endif
      @endif

      <li class="nav-item nav-category">System</li>
      @if(auth()->user()->hasPermission('roles.view'))
      <li class="nav-item {{ Request::is('admin/roles*') ? 'active' : '' }}">
        <a href="{{ route('admin.roles.index') }}" class="nav-link">
          <i class="link-icon" data-feather="shield"></i>
          <span class="link-title">Roles & Permissions</span>
        </a>
      </li>
      @endif
      @if(auth()->user()->hasPermission('audit_logs.view'))
      <li class="nav-item {{ Request::is('admin/audit-logs*') ? 'active' : '' }}">
        <a href="{{ route('admin.audit-logs.index') }}" class="nav-link">
          <i class="link-icon" data-feather="file-text"></i>
          <span class="link-title">Audit Logs</span>
        </a>
      </li>
      @endif
      @if(auth()->user()->hasPermission('reports.view'))
      <li class="nav-item {{ Request::is('admin/reports*') ? 'active' : '' }}">
        <a href="{{ route('admin.reports.index') }}" class="nav-link">
          <i class="link-icon" data-feather="bar-chart-2"></i>
          <span class="link-title">Reports</span>
        </a>
      </li>
      @endif
      @if(auth()->user()->hasPermission('shipping_rates.view'))
      <li class="nav-item {{ Request::is('admin/shipping-rates*') ? 'active' : '' }}">
        <a href="{{ route('admin.shipping-rates.index') }}" class="nav-link">
          <i class="link-icon" data-feather="navigation"></i>
          <span class="link-title">Shipping Rates</span>
        </a>
      </li>
      @endif
      @if(auth()->user()->hasPermission('settings.view'))
      <li class="nav-item {{ Request::is('admin/contact-settings*') ? 'active' : '' }}">
        <a href="{{ route('admin.settings.contact') }}" class="nav-link">
          <i class="link-icon" data-feather="share-2"></i>
          <span class="link-title">Contact & Socials</span>
        </a>
      </li>
      <li class="nav-item {{ Request::is('admin/settings*') ? 'active' : '' }}">
        <a href="{{ route('admin.settings.index') }}" class="nav-link">
          <i class="link-icon" data-feather="settings"></i>
          <span class="link-title">Settings</span>
        </a>
      </li>
      @endif

    </ul>
  </div>
</nav>
