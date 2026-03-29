<nav class="navbar">
  <a href="#" class="sidebar-toggler">
    <i data-feather="menu"></i>
  </a>
  <div class="navbar-content">
    <form class="search-form">
      <div class="input-group">
        <div class="input-group-prepend">
          <div class="input-group-text">
            <i data-feather="search"></i>
          </div>
        </div>
        <input type="text" class="form-control" id="navbarForm" placeholder="Search here...">
      </div>
    </form>
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center text-nowrap" href="{{ route('home') }}" target="_blank" title="Visit Store">
          <i data-feather="globe"></i>
          <span class="ml-1 d-none d-md-inline-block">Visit Store</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" id="adminThemeToggle" title="Toggle Theme">
          <i data-feather="moon"></i>
        </a>
      </li>
      <li class="nav-item dropdown nav-notifications">
        <a class="nav-link dropdown-toggle" href="#" id="adminNotificationMenuTrigger" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i data-feather="bell"></i>
          @php $unreadAdminNotifs = auth()->user()->unreadNotifications()->count(); @endphp
          @if($unreadAdminNotifs > 0)
          <div class="indicator" id="adminNotificationIndicator">
            <div class="circle"></div>
          </div>
          @endif
        </a>
        <div class="dropdown-menu" aria-labelledby="adminNotificationMenuTrigger" style="width: 350px;">
          <div class="dropdown-header d-flex align-items-center justify-content-between">
            <p class="mb-0 font-weight-medium">Notifications</p>
          </div>
          <div class="dropdown-body" id="adminNotificationListContent" style="max-height: 400px; overflow-y: auto;">
             <div class="text-center p-4"><div class="spinner-border spinner-border-sm text-primary"></div></div>
          </div>
        </div>
      </li>
      <li class="nav-item dropdown nav-profile">
        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <img src="{{ asset('admin_assets/images/user.png') }}" onerror="this.onerror=null; this.src='{{ asset('admin_assets/images/placeholder.jpg') }}'" alt="profile">
        </a>
        <div class="dropdown-menu" aria-labelledby="profileDropdown">
          <div class="dropdown-header d-flex flex-column align-items-center">
            <div class="figure mb-3">
              <img src="{{ asset('admin_assets/images/user.png') }}" onerror="this.onerror=null; this.src='{{ asset('admin_assets/images/placeholder.jpg') }}'" alt="">
            </div>
            <div class="info text-center">
              <p class="name font-weight-bold mb-0">{{ Auth::user()->name }}</p>
              <p class="email text-muted mb-3">{{ Auth::user()->email }}</p>
            </div>
          </div>
          <div class="dropdown-body">
            <ul class="profile-nav p-0 pt-3">
              <li class="nav-item">
                <a href="{{ route('admin.profile') }}" class="nav-link">
                  <i data-feather="user"></i>
                  <span>Profile</span>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.profile') }}" class="nav-link">
                  <i data-feather="edit"></i>
                  <span>Edit Profile</span>
                </a>
              </li>
              <li class="nav-item">
                <a href="javascript:;" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                  <i data-feather="log-out"></i>
                  <span>Log Out</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </li>
      <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: none;">
        @csrf
      </form>
    </ul>
  </div>
</nav>
