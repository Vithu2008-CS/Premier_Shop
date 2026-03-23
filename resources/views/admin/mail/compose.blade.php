@extends('layouts.admin_noble')

@section('title', 'Compose Message')

@push('plugin-styles')
  <link rel="stylesheet" href="{{ asset('admin_assets/vendors/select2/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('admin_assets/vendors/simplemde/simplemde.min.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
    .editor-toolbar a { color: #333 !important; }
    .editor-toolbar a.active, .editor-toolbar a:hover { color: #000 !important; background: #f0f0f0; border-color: #f0f0f0; }
    .editor-toolbar i { color: inherit !important; }
  </style>
@endpush

@section('content')
<div class="row inbox-wrapper">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @include('admin.mail.partials.sidebar')
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
                                                <select name="to[]" class="compose-multiple-select form-control w-100" multiple="multiple">
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
                                            <button type="submit" name="save_draft" value="1" class="btn btn-info btn-space"><i class="icon s7-file"></i> Save as Draft</button>
                                            <a href="{{ route('admin.mail.inbox') }}" class="btn btn-secondary btn-space"><i class="icon s7-close"></i> Cancel</a>
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

        // Ensure SimpleMDE content is synced to the textarea on form submit
        $('form').on('submit', function() {
          $("#simpleMdeEditor").val(simplemde.value());
        });
      }
    });
  </script>
@endpush
