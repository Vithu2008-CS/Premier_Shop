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
      <li class="nav-item nav-category">Web Apps</li>
      <li class="nav-item {{ Request::is('admin/mail*') ? 'active' : '' }}">
        <a href="{{ route('admin.mail.inbox') }}" class="nav-link">
          <i class="link-icon" data-feather="mail"></i>
          <span class="link-title">Email</span>
        </a>
      </li>
      <li class="nav-item nav-category">Shop Management</li>
      
      <li class="nav-item {{ Request::is('admin/categories*') ? 'active' : '' }}">
        <a href="{{ route('admin.categories.index') }}" class="nav-link">
          <i class="link-icon" data-feather="layers"></i>
          <span class="link-title">Categories</span>
        </a>
      </li>

      <li class="nav-item {{ Request::is('admin/products*') ? 'active' : '' }}">
        <a href="{{ route('admin.products.index') }}" class="nav-link">
          <i class="link-icon" data-feather="shopping-bag"></i>
          <span class="link-title">Products</span>
        </a>
      </li>

      <li class="nav-item {{ Request::is('admin/orders*') ? 'active' : '' }}">
        <a href="{{ route('admin.orders.index') }}" class="nav-link">
          <i class="link-icon" data-feather="shopping-cart"></i>
          <span class="link-title">Orders</span>
        </a>
      </li>

      <li class="nav-item {{ Request::is('admin/customers*') ? 'active' : '' }}">
        <a href="{{ route('admin.customers.index') }}" class="nav-link">
          <i class="link-icon" data-feather="users"></i>
          <span class="link-title">Customers</span>
        </a>
      </li>

      <li class="nav-item {{ Request::is('admin/drivers*') ? 'active' : '' }}">
        <a href="{{ route('admin.drivers.index') }}" class="nav-link">
          <i class="link-icon" data-feather="truck"></i>
          <span class="link-title">Drivers</span>
        </a>
      </li>

      <li class="nav-item nav-category">Promotions</li>
      <li class="nav-item {{ Request::is('admin/coupons*') ? 'active' : '' }}">
        <a href="{{ route('admin.coupons.index') }}" class="nav-link">
          <i class="link-icon" data-feather="tag"></i>
          <span class="link-title">Coupons</span>
        </a>
      </li>
      <li class="nav-item {{ Request::is('admin/sliders*') ? 'active' : '' }}">
        <a href="{{ route('admin.sliders.index') }}" class="nav-link">
          <i class="link-icon" data-feather="image"></i>
          <span class="link-title">Home Sliders</span>
        </a>
      </li>

      <li class="nav-item nav-category">System</li>
      <li class="nav-item {{ Request::is('admin/roles*') ? 'active' : '' }}">
        <a href="{{ route('admin.roles.index') }}" class="nav-link">
          <i class="link-icon" data-feather="shield"></i>
          <span class="link-title">Roles & Permissions</span>
        </a>
      </li>
      <li class="nav-item {{ Request::is('admin/reports*') ? 'active' : '' }}">
        <a href="{{ route('admin.reports.index') }}" class="nav-link">
          <i class="link-icon" data-feather="bar-chart-2"></i>
          <span class="link-title">Reports</span>
        </a>
      </li>
      <li class="nav-item {{ Request::is('admin/settings*') ? 'active' : '' }}">
        <a href="{{ route('admin.settings.index') }}" class="nav-link">
          <i class="link-icon" data-feather="settings"></i>
          <span class="link-title">Settings</span>
        </a>
      </li>


    </ul>
  </div>
</nav>
