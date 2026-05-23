{{--
    emails/admin_custom.blade.php — Admin custom mail template
    ============================================================
    Body rendered via {!! $mailMessage !!} (pre-rendered from Str::markdown() in AdminCustomMail).
    Subject and recipient set by AdminCustomMail mailable from MailController::send().
    Extends emails/layouts/master.blade.php.
--}}
@extends('emails.layouts.master')
@section('title', 'Message from Premier Shop Admin')
@section('header_subtitle', 'Important Update')

@section('content')
<tr>
    <td style="padding:40px;text-align:left;font-size:16px;color:#2d3436;line-height:1.6;">
        {!! $mailMessage !!}
    </td>
</tr>
@endsection
