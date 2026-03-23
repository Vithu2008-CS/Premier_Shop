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
