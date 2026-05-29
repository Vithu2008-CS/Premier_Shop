{{--
    admin/mail/read.blade.php — Single message reader
    ===================================================
    Shows full message body, sender info, tags, timestamps.
    Reply/forward/delete actions. Marks message as read on load via MailController::show().
    Variable: $message (ContactMessage)
--}}
@extends('layouts.admin_noble')

@section('title', 'Read Message')

@section('content')
<div class="row inbox-wrapper">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @include('admin.mail.partials.sidebar')
                    <div class="col-lg-9 email-content">

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            </div>
                        @endif

                        <div class="email-head">
                            <div class="email-head-subject">
                                <div class="title d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <form action="{{ route('admin.mail.star', $message->id) }}" method="POST" class="d-inline mr-2">
                                            @csrf
                                            <button type="submit" class="p-0 border-0 bg-transparent icon" style="outline:none;">
                                                <i data-feather="star" class="{{ $message->is_starred ? 'text-warning fill-warning' : 'text-muted' }}"></i>
                                            </button>
                                        </form>
                                        <span>{{ $message->subject }}</span>
                                    </div>
                                    <div class="icons d-flex align-items-center">
                                        <a href="{{ route('admin.mail.compose', ['to' => $message->email, 'subject' => 'Re: ' . $message->subject]) }}"
                                           class="icon mr-2" data-toggle="tooltip" title="Reply">
                                            <i data-feather="corner-up-left" class="text-muted"></i>
                                        </a>
                                        <a href="javascript:window.print();" class="icon mr-2">
                                            <i data-feather="printer" class="text-muted" data-toggle="tooltip" title="Print"></i>
                                        </a>
                                        <form action="{{ route('admin.mail.markUnread', $message->id) }}" method="POST" class="d-inline mr-2">
                                            @csrf
                                            <button type="submit" class="p-0 border-0 bg-transparent icon" data-toggle="tooltip" title="Mark as unread">
                                                <i data-feather="mail" class="text-muted"></i>
                                            </button>
                                        </form>
                                        @if($message->is_trash)
                                            <form action="{{ route('admin.mail.restore', $message->id) }}" method="POST" class="d-inline mr-2">
                                                @csrf
                                                <button type="submit" class="p-0 border-0 bg-transparent icon" data-toggle="tooltip" title="Restore">
                                                    <i data-feather="rotate-ccw" class="text-muted"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.mail.destroy', $message->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this message?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-0 border-0 bg-transparent icon">
                                                <i data-feather="trash" class="text-muted" data-toggle="tooltip"
                                                   title="{{ $message->is_trash ? 'Delete permanently' : 'Move to trash' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="email-head-sender d-flex align-items-center justify-content-between flex-wrap">
                                <div class="d-flex align-items-center">
                                    <div class="avatar mr-2">
                                        @php
                                            $initials = collect(explode(' ', $message->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                                            $colors = ['4e73df','1cc88a','36b9cc','e74a3b','f6c23e','858796'];
                                            $color  = $colors[crc32($message->email) % count($colors)];
                                        @endphp
                                        <div style="width:36px;height:36px;border-radius:50%;background:#{{ $color }};
                                                    display:flex;align-items:center;justify-content:center;
                                                    color:#fff;font-size:13px;font-weight:700;">
                                            {{ $initials }}
                                        </div>
                                    </div>
                                    <div class="sender d-flex align-items-center">
                                        <a href="javascript:;">{{ $message->name }}</a>
                                        <span class="ml-1">&lt;{{ $message->email }}&gt;</span>
                                    </div>
                                </div>
                                <div class="date">{{ $message->created_at->format('M d, Y H:i') }}</div>
                            </div>
                        </div>

                        <div class="email-body">
                            @if(\Illuminate\Support\Str::contains($message->message, ['<html', '<body', '<table']))
                                <iframe srcdoc="{{ $message->message }}"
                                        style="width:100%;height:800px;border:none;overflow:hidden;border-radius:8px;"></iframe>
                            @else
                                {!! nl2br(e($message->message)) !!}
                            @endif

                            @if($message->phone)
                                <hr>
                                <p><strong>Phone:</strong> {{ $message->phone }}</p>
                            @endif
                        </div>

                        <div class="email-reply mt-4">
                            <a href="{{ route('admin.mail.compose', ['to' => $message->email, 'subject' => 'Re: ' . $message->subject]) }}"
                               class="btn btn-primary d-inline-flex align-items-center justify-content-center" style="height: 38px; border-radius: 20px; font-size: 0.875rem; padding: 0 18px;">
                                <i data-feather="corner-up-left" style="width: 16px; height: 16px; margin-right: 6px;"></i>
                                <span>Reply</span>
                            </a>
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center ml-2" style="height: 38px; border-radius: 20px; font-size: 0.875rem; padding: 0 18px;">
                                <i data-feather="arrow-left" style="width: 16px; height: 16px; margin-right: 6px;"></i>
                                <span>Back</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
