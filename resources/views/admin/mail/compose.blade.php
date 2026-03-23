@extends('layouts.admin_noble')

@section('title', 'Compose Message')

@push('plugin-styles')
  <link rel="stylesheet" href="{{ asset('admin_assets/vendors/select2/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('admin_assets/vendors/simplemde/simplemde.min.css') }}">
@endpush

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
                                    <li>
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
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9 email-content">
                        <div class="email-head">
                            <div class="email-head-title d-flex align-items-center">
                                <span data-feather="edit" class="icon-md mr-2"></span>
                                New message
                            </div>
                        </div>
                        <form action="{{ route('admin.mail.send') }}" method="POST">
                            @csrf
                            <div class="email-compose-fields">
                                <div class="to">
                                    <div class="form-group row py-0">
                                        <label class="col-md-1 control-label">To:</label>
                                        <div class="col-md-11">
                                            <div class="form-group">
                                                <select name="to" class="compose-multiple-select form-control w-100" multiple="multiple">
                                                    @if($to)
                                                        <option value="{{ $to }}" selected>{{ $to }}</option>
                                                    @endif
                                                    <option value="newsletter">All Newsletter Subscribers</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="subject">
                                    <div class="form-group row py-0">
                                        <label class="col-md-1 control-label">Subject</label>
                                        <div class="col-md-11">
                                            <input name="subject" class="form-control" type="text" value="{{ $subject }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="email editor">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label sr-only" for="simpleMdeEditor">Message</label>
                                        <textarea name="message" class="form-control" id="simpleMdeEditor" rows="10"></textarea>
                                    </div>
                                </div>
                                <div class="email action-send">
                                    <div class="col-md-12">
                                        <div class="form-group mb-0">
                                            <button class="btn btn-primary btn-space" type="submit"><i class="icon s7-mail"></i> Send</button>
                                            <a href="{{ route('admin.mail.inbox') }}" class="btn btn-secondary btn-space" type="button"><i class="icon s7-close"></i> Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('plugin-scripts')
  <script src="{{ asset('admin_assets/vendors/select2/select2.min.js') }}"></script>
  <script src="{{ asset('admin_assets/vendors/simplemde/simplemde.min.js') }}"></script>
@endpush

@push('scripts')
  <script>
    $(function() {
      'use strict';

      // Select2
      if ($(".compose-multiple-select").length) {
        $(".compose-multiple-select").select2({
            tags: true,
            tokenSeparators: [',', ' ']
        });
      }

      // SimpleMDE
      if ($("#simpleMdeEditor").length) {
        var simplemde = new SimpleMDE({
          element: $("#simpleMdeEditor")[0]
        });
      }
    });
  </script>
@endpush
