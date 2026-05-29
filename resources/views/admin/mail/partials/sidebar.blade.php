{{--
    admin/mail/partials/sidebar.blade.php — Mail centre navigation sidebar
    ========================================================================
    Folder links: Inbox, Sent, Drafts, Starred, Trash with unread/count badges.
    Active folder highlighted via Route::currentRouteName() comparison.
    Included by inbox.blade.php, read.blade.php, compose.blade.php.
--}}
@php
    $currentRoute = Route::currentRouteName();
@endphp
<div class="col-lg-3 email-aside border-lg-right">
    <div class="aside-content">
        <div class="aside-header">
            <button class="navbar-toggle" data-target=".aside-nav" data-toggle="collapse" type="button">
                <span class="icon"><i data-feather="chevron-down"></i></span>
            </button>
            <span class="title">Mail Service</span>
            <p class="description">{{ auth()->user()->email }}</p>
        </div>
        <div class="aside-compose">
            <a class="btn btn-primary btn-block d-inline-flex align-items-center justify-content-center" href="{{ route('admin.mail.compose') }}" style="height: 38px; border-radius: 20px; font-weight: 600; font-size: 0.875rem;">
                <i data-feather="edit" style="width: 14px; height: 14px; margin-right: 6px;"></i>
                <span>Compose Email</span>
            </a>
        </div>
        <div class="aside-nav collapse">
            <ul class="nav">
                <li class="{{ $currentRoute == 'admin.mail.inbox' ? 'active' : '' }}">
                    <a href="{{ route('admin.mail.inbox') }}">
                        <span class="icon"><i data-feather="inbox"></i></span>Inbox
                        @if(isset($unreadCount) && $unreadCount > 0)
                            <span class="badge badge-danger-muted text-white font-weight-bold float-right">{{ $unreadCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="{{ $currentRoute == 'admin.mail.sent' ? 'active' : '' }}">
                    <a href="{{ route('admin.mail.sent') }}"><span class="icon"><i data-feather="mail"></i></span>Sent Mail</a>
                </li>
                <li class="{{ $currentRoute == 'admin.mail.important' ? 'active' : '' }}">
                    <a href="{{ route('admin.mail.important') }}"><span class="icon"><i data-feather="briefcase"></i></span>Important</a>
                </li>
                <li class="{{ $currentRoute == 'admin.mail.drafts' ? 'active' : '' }}">
                    <a href="{{ route('admin.mail.drafts') }}"><span class="icon"><i data-feather="file"></i></span>Drafts</a>
                </li>
                <li class="{{ $currentRoute == 'admin.mail.tags' ? 'active' : '' }}">
                    <a href="{{ route('admin.mail.tags') }}"><span class="icon"><i data-feather="star"></i></span>Tags</a>
                </li>
                <li class="{{ $currentRoute == 'admin.mail.trash' ? 'active' : '' }}">
                    <a href="{{ route('admin.mail.trash') }}"><span class="icon"><i data-feather="trash"></i></span>Trash</a>
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
.email-aside .aside-nav .nav li a {
    border-radius: 20px !important;
    padding: 10px 18px !important;
    margin-bottom: 4px !important;
    transition: all 0.2s ease !important;
}
.email-aside .aside-nav .nav li.active a {
    background-color: rgba(108, 92, 231, 0.15) !important;
    color: #6c5ce7 !important;
    font-weight: 700 !important;
}
html[data-admin-theme="dark"] .email-aside .aside-nav .nav li.active a {
    background-color: rgba(167, 139, 250, 0.2) !important;
    color: #a78bfa !important;
}
.email-aside .aside-nav .nav li a:hover {
    background-color: rgba(0, 0, 0, 0.04) !important;
}
html[data-admin-theme="dark"] .email-aside .aside-nav .nav li a:hover {
    background-color: rgba(255, 255, 255, 0.05) !important;
}
</style>
