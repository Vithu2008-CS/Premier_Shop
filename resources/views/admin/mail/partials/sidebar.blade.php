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
            <a class="btn btn-primary btn-block" href="{{ route('admin.mail.compose') }}">Compose Email</a>
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
