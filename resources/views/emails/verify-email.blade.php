@php
    $border = '#52525b';
    $text = '#e4e4e7';
    $muted = '#a1a1aa';
    $primary = '#2563eb';
    $fontStack = "'Instrument Sans', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif";
@endphp
<x-mail.layout>
    <x-slot:preheader>{{ __('One click and you\'re in.') }}</x-slot:preheader>

    <h1 style="margin:0 0 16px 0; font-family:{{ $fontStack }}; font-size:24px; font-weight:600; color:{{ $text }}; line-height:1.3;">
        {{ __('Hey, welcome aboard.') }}
    </h1>

    <p style="margin:0 0 16px 0; font-family:{{ $fontStack }}; font-size:15px; line-height:1.6; color:{{ $text }};">
        {{ __("Quick one before you get started. Confirm your email below and your registry is ready to push and pull.") }}
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:28px 0;">
        <tr>
            <td style="background-color:{{ $primary }};">
                <a href="{{ $url }}"
                   style="display:inline-block; padding:12px 24px; font-family:{{ $fontStack }}; font-size:15px; font-weight:600; color:#ffffff; text-decoration:none;">
                    {{ __('Confirm my email') }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 24px 0; font-family:{{ $fontStack }}; font-size:13px; line-height:1.6; color:{{ $muted }};">
        {{ __('Link\'s good for :minutes minutes. Didn\'t sign up? Just ignore this, no harm done.', ['minutes' => config('auth.verification.expire', 60)]) }}
    </p>

    <hr style="border:none; border-top:1px dashed {{ $border }}; margin:24px 0;">

    <p style="margin:0; font-family:{{ $fontStack }}; font-size:13px; line-height:1.6; color:{{ $muted }};">
        {{ __('Button not working?') }}
        <a href="{{ $url }}" style="color:{{ $muted }}; text-decoration:underline;">{{ __('Use this link instead.') }}</a>
    </p>
</x-mail.layout>
