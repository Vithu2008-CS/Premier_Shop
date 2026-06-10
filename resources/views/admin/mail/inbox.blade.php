{{--
    admin/mail/inbox.blade.php — Mail centre folder view (inbox/sent/trash/starred)
    =================================================================================
    Reused for all folders; $pageTitle and $folder variable control active state.
    Lists ContactMessage records filtered by folder. Search, star toggle, bulk delete.
    Sidebar partial: admin/mail/partials/sidebar.blade.php
    Variables: $messages (paginated), $folder, $pageTitle
--}}
@extends('layouts.admin_noble')

@section('title', $pageTitle ?? 'Inbox')

@section('content')
<div class="row inbox-wrapper">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @include('admin.mail.partials.sidebar')
                    <div class="col-lg-9 email-content">
                        <div class="email-inbox-header">
                            <div class="row align-items-center">
                                <div class="col-lg-6">
                                    <div class="email-title mb-2 mb-md-0">
                                        <span class="icon"><i data-feather="{{ $pageIcon ?? 'inbox' }}"></i></span> {{ $pageTitle ?? 'Inbox' }}
                                        @if($unreadCount > 0 && ($pageTitle ?? 'Inbox') === 'Inbox')
                                            <span class="new-messages">({{ $unreadCount }} new messages)</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <form action="{{ route('admin.mail.search') }}" method="GET" class="email-search d-flex justify-content-end">
                                        <div class="input-group" style="width: 100%; max-width: 280px;">
                                            <input class="form-control font-weight-medium" type="text" name="q" placeholder="Search mail..." value="{{ request('q') }}" style="height: 38px; border-radius: 20px 0 0 20px; font-size: 0.875rem;">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit" style="height: 38px; border-radius: 0 20px 20px 0; padding: 0 16px; display: flex; align-items: center; justify-content: center;">
                                                    <i data-feather="search" class="icon-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            </div>
                        @endif

                        <div class="email-filters d-flex align-items-center justify-content-between flex-wrap">
                            <div class="email-filters-left flex-wrap d-none d-md-flex">
                                <div class="form-check form-check-flat form-check-primary">
                                    <label class="form-check-label">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </label>
                                </div>
                            </div>
                            <div class="email-filters-right">
                                @if($messages->total() > 0)
                                    <span class="email-pagination-indicator">
                                        {{ $messages->firstItem() }}&ndash;{{ $messages->lastItem() }} of {{ $messages->total() }}
                                    </span>
                                @else
                                    <span class="email-pagination-indicator">0 messages</span>
                                @endif
                                <div class="btn-group email-pagination-nav">
                                    <a href="{{ $messages->previousPageUrl() }}" class="btn btn-outline-secondary btn-icon {{ $messages->onFirstPage() ? 'disabled' : '' }}" style="border-radius: 20px 0 0 20px; width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;"><i data-feather="chevron-left" style="width: 16px; height: 16px;"></i></a>
                                    <a href="{{ $messages->nextPageUrl() }}" class="btn btn-outline-secondary btn-icon {{ !$messages->hasMorePages() ? 'disabled' : '' }}" style="border-radius: 0 20px 20px 0; width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;"><i data-feather="chevron-right" style="width: 16px; height: 16px;"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="email-list">
                            @forelse($messages as $msg)
                                <div class="email-list-item {{ !$msg->is_read ? 'email-list-item--unread' : '' }}">
                                    <div class="email-list-actions">
                                        <div class="form-check form-check-flat form-check-primary">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input msg-checkbox" value="{{ $msg->id }}">
                                            </label>
                                        </div>
                                        <form action="{{ route('admin.mail.star', $msg->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="favorite p-0 border-0 bg-transparent" style="cursor:pointer;outline:none;">
                                                <span><i data-feather="star" class="{{ $msg->is_starred ? 'text-warning fill-warning' : '' }}"></i></span>
                                            </button>
                                        </form>
                                    </div>

                                    @if($msg->folder === 'draft')
                                        <a href="{{ route('admin.mail.compose', ['draft_id' => $msg->id]) }}" class="email-list-detail">
                                    @else
                                        <a href="{{ route('admin.mail.read', $msg->id) }}" class="email-list-detail">
                                    @endif
                                        <div class="content">
                                            <span class="from">{{ $msg->name }}</span>
                                            <p class="msg">{{ $msg->subject }} &mdash; {{ Str::limit(strip_tags($msg->message), 60) }}</p>
                                        </div>
                                        <span class="date">{{ $msg->created_at->format('d M') }}</span>
                                    </a>

                                    <div class="email-list-item-actions d-flex align-items-center ml-2">
                                        @if($msg->is_trash)
                                            <form action="{{ route('admin.mail.restore', $msg->id) }}" method="POST" class="d-inline mr-1">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-outline-success d-inline-flex align-items-center justify-content-center" title="Restore" style="border-radius: 50%; width: 26px; height: 26px; padding: 0;">
                                                    <i data-feather="rotate-ccw" style="width:13px;height:13px;"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.mail.destroy', $msg->id) }}" method="POST" class="d-inline"
                                              data-confirm="Delete this message?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger d-inline-flex align-items-center justify-content-center" title="{{ $msg->is_trash ? 'Delete permanently' : 'Move to trash' }}" style="border-radius: 50%; width: 26px; height: 26px; padding: 0;">
                                                <i data-feather="trash-2" style="width:13px;height:13px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="p-5 text-center">
                                    <i data-feather="inbox" style="width:48px;height:48px;" class="text-muted mb-3"></i>
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

@push('scripts')
<script nonce="{{ Vite::cspNonce() }}">
    document.getElementById('selectAll')?.addEventListener('change', function () {
        document.querySelectorAll('.msg-checkbox').forEach(cb => cb.checked = this.checked);
    });
</script>
@endpush
