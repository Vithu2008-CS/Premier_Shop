{{--
    admin/mail/compose.blade.php — Compose / edit draft email
    ===========================================================
    Select2 recipient picker, SimpleMDE markdown body editor.
    Send → MailController::send() stores in sent folder + dispatches Mailable.
    Save draft → MailController::saveDraft() stores in draft folder (no send).
    $draftId non-null → editing existing draft; form pre-populated.
    Variables: $customers (for recipient picker), $draftId?, $draft? (ContactMessage)
--}}
@extends('layouts.admin_noble')

@section('title', isset($draftId) && $draftId ? 'Edit Draft' : 'Compose Message')

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
                                {{ isset($draftId) && $draftId ? 'Edit Draft' : 'New Message' }}
                            </div>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger mb-3">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.mail.send') }}" method="POST">
                            @csrf
                            @if(isset($draftId) && $draftId)
                                <input type="hidden" name="draft_id" value="{{ $draftId }}">
                            @endif
                            <div class="email-compose-fields">
                                <div class="to">
                                    <div class="form-group row py-0">
                                        <label class="col-md-1 control-label">To:</label>
                                        <div class="col-md-11">
                                            <select name="to[]" class="compose-multiple-select form-control w-100" multiple="multiple">
                                                @if($to)
                                                    @foreach(explode(',', $to) as $addr)
                                                        @php $addr = trim($addr); @endphp
                                                        @if($addr)
                                                            <option value="{{ $addr }}" selected>{{ $addr }}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                <option value="newsletter" {{ str_contains($to ?? '', 'newsletter') ? 'selected' : '' }}>
                                                    All Newsletter Subscribers
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="subject">
                                    <div class="form-group row py-0">
                                        <label class="col-md-1 control-label">Subject</label>
                                        <div class="col-md-11">
                                            <input name="subject" class="form-control" type="text"
                                                   value="{{ old('subject', $subject ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="email editor">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label sr-only" for="simpleMdeEditor">Message</label>
                                        <textarea name="message" class="form-control" id="simpleMdeEditor" rows="10">{{ old('message', $body ?? '') }}</textarea>
                                    </div>
                                </div>
                                <div class="email action-send">
                                    <div class="col-md-12">
                                        <div class="form-group mb-0">
                                            <button class="btn btn-primary btn-space" type="submit">
                                                <i data-feather="send" class="icon-sm mr-1"></i> Send
                                            </button>
                                            <button type="submit" name="save_draft" value="1" class="btn btn-info btn-space">
                                                <i data-feather="save" class="icon-sm mr-1"></i> Save as Draft
                                            </button>
                                            <a href="{{ route('admin.mail.inbox') }}" class="btn btn-secondary btn-space">
                                                <i data-feather="x" class="icon-sm mr-1"></i> Cancel
                                            </a>
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
    $(function () {
      'use strict';

      if ($(".compose-multiple-select").length) {
        $(".compose-multiple-select").select2({
          tags: true,
          tokenSeparators: [',', ' '],
          placeholder: 'Add recipients...'
        });
      }

      if ($("#simpleMdeEditor").length) {
        var simplemde = new SimpleMDE({ element: $("#simpleMdeEditor")[0] });

        $('form').on('submit', function () {
          $("#simpleMdeEditor").val(simplemde.value());
        });
      }
    });
  </script>
@endpush
