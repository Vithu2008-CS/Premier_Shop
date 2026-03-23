@extends('layouts.admin_noble')

@section('title', 'Inbox')

@section('content')
<div class="row inbox-wrapper">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
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
                                    <li class="active">
                                        <a href="{{ route('admin.mail.inbox') }}">
                                            <span class="icon"><i data-feather="inbox"></i></span>Inbox
                                            @if($unreadCount > 0)
                                                <span class="badge badge-danger-muted text-white font-weight-bold float-right">{{ $unreadCount }}</span>
                                            @endif
                                        </a>
                                    </li>
                                    <li><a href="javascript:;"><span class="icon"><i data-feather="mail"></i></span>Sent Mail</a></li>
                                    <li><a href="javascript:;"><span class="icon"><i data-feather="briefcase"></i></span>Important</a></li>
                                    <li><a href="javascript:;"><span class="icon"><i data-feather="file"></i></span>Drafts</a></li>
                                    <li><a href="javascript:;"><span class="icon"><i data-feather="star"></i></span>Tags</a></li>
                                    <li><a href="javascript:;"><span class="icon"><i data-feather="trash"></i></span>Trash</a></li>
                                </ul>
                                <span class="title">Labels</span>
                                <ul class="nav nav-pills nav-stacked">
                                    <li>
                                        <a href="javascript:;"><i data-feather="tag" class="text-warning"></i> Important </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;"><i data-feather="tag" class="text-primary"></i> Business </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;"><i data-feather="tag" class="text-info"></i> Inspiration </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9 email-content">
                        <div class="email-inbox-header">
                            <div class="row align-items-center">
                                <div class="col-lg-6">
                                    <div class="email-title mb-2 mb-md-0">
                                        <span class="icon"><i data-feather="inbox"></i></span> Inbox 
                                        @if($unreadCount > 0)
                                            <span class="new-messages">({{ $unreadCount }} new messages)</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="email-search">
                                        <div class="input-group input-search">
                                            <input class="form-control" type="text" placeholder="Search mail...">
                                            <span class="input-group-btn">
                                                <button class="btn btn-outline-secondary" type="button"><i data-feather="search"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="email-filters d-flex align-items-center justify-content-between flex-wrap">
                            <div class="email-filters-left flex-wrap d-none d-md-flex">
                                <div class="form-check form-check-flat form-check-primary">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input">
                                    </label>
                                </div>
                                <div class="btn-group ml-3">
                                    <button class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" type="button"> With selected <span class="caret"></span></button>
                                    <div class="dropdown-menu" role="menu">
                                        <a class="dropdown-item" href="javascript:;">Mark as read</a>
                                        <a class="dropdown-item" href="javascript:;">Mark as unread</a>
                                        <a class="dropdown-item" href="javascript:;">Spam</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="javascript:;">Delete</a>
                                    </div>
                                </div>
                                <div class="btn-group mb-1 mb-md-0">
                                    <button class="btn btn-outline-primary" type="button">Archive</button>
                                    <button class="btn btn-outline-primary" type="button">Spam</button>
                                    <button class="btn btn-outline-primary" type="button">Delete</button>
                                </div>
                            </div>
                            <div class="email-filters-right">
                                <span class="email-pagination-indicator">{{ $messages->firstItem() }}-{{ $messages->lastItem() }} of {{ $messages->total() }}</span>
                                <div class="btn-group email-pagination-nav">
                                    <a href="{{ $messages->previousPageUrl() }}" class="btn btn-outline-secondary btn-icon {{ $messages->onFirstPage() ? 'disabled' : '' }}"><i data-feather="chevron-left"></i></a>
                                    <a href="{{ $messages->nextPageUrl() }}" class="btn btn-outline-secondary btn-icon {{ !$messages->hasMorePages() ? 'disabled' : '' }}"><i data-feather="chevron-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="email-list">
                            @forelse($messages as $msg)
                                <div class="email-list-item {{ !$msg->is_read ? 'email-list-item--unread' : '' }}">
                                    <div class="email-list-actions">
                                        <div class="form-check form-check-flat form-check-primary">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input">
                                            </label>
                                        </div>
                                        <a class="favorite" href="javascript:;"><span><i data-feather="star"></i></span></a>
                                    </div>
                                    <a href="{{ route('admin.mail.read', $msg->id) }}" class="email-list-detail">
                                        <div class="content">
                                            <span class="from">{{ $msg->name }}</span>
                                            <p class="msg">{{ $msg->subject }} - {{ Str::limit($msg->message, 50) }}</p>
                                        </div>
                                        <span class="date">
                                            {{ $msg->created_at->format('d M') }}
                                        </span>
                                    </a>
                                </div>
                            @empty
                                <div class="p-4 text-center">
                                    <p class="text-muted">No messages found.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
