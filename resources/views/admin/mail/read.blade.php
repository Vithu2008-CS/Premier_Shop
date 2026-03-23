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
                        <div class="email-head">
                            <div class="email-head-subject">
                                <div class="title d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <form action="{{ route('admin.mail.star', $message->id) }}" method="POST" class="d-inline mr-2">
                                            @csrf
                                            <button type="submit" class="p-0 border-0 bg-transparent icon" style="outline: none;">
                                                <i data-feather="star" class="{{ $message->is_starred ? 'text-warning fill-warning' : 'text-primary-muted' }}"></i>
                                            </button>
                                        </form> 
                                        <span>{{ $message->subject }}</span>
                                    </div>
                                    <div class="icons">
                                        <a href="{{ route('admin.mail.compose', ['to' => $message->email, 'subject' => 'Re: ' . $message->subject]) }}" class="icon" data-toggle="tooltip" title="Reply"><i data-feather="share" class="text-muted hover-primary-muted"></i></a>
                                        <a href="javascript:window.print();" class="icon"><i data-feather="printer" class="text-muted" data-toggle="tooltip" title="Print"></i></a>
                                        <form action="{{ route('admin.mail.destroy', $message->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this message?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-0 border-0 bg-transparent icon"><i data-feather="trash" class="text-muted" data-toggle="tooltip" title="Delete"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="email-head-sender d-flex align-items-center justify-content-between flex-wrap">
                                <div class="d-flex align-items-center">
                                    <div class="avatar">
                                        <img src="https://via.placeholder.com/36x36" alt="Avatar" class="rounded-circle user-avatar-md">
                                    </div>
                                    <div class="sender d-flex align-items-center">
                                        <a href="javascript:;">{{ $message->name }}</a> <span>&lt;{{ $message->email }}&gt;</span>
                                        <div class="actions dropdown">
                                            <a class="icon" href="javascript:;" data-toggle="dropdown"><i data-feather="chevron-down"></i></a>
                                            <div class="dropdown-menu" role="menu">
                                                <a class="dropdown-item" href="javascript:;">Mark as unread</a>
                                                <a class="dropdown-item" href="javascript:;">Spam</a>
                                                <div class="dropdown-divider"></div>
                                                <form action="{{ route('admin.mail.destroy', $message->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="date">{{ $message->created_at->format('M d, Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="email-body">
                            @if(\Illuminate\Support\Str::contains($message->message, ['<html', '<body', '<table']))
                                <iframe srcdoc="{{ $message->message }}" style="width: 100%; height: 800px; border: none; overflow: hidden; border-radius: 8px;"></iframe>
                            @else
                                {!! nl2br(e($message->message)) !!}
                            @endif
                            
                            @if($message->phone)
                                <hr>
                                <p><strong>Phone:</strong> {{ $message->phone }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
