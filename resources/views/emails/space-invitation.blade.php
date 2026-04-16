@php
    $border = '#52525b';
    $text = '#e4e4e7';
    $muted = '#a1a1aa';
    $primary = '#2563eb';
    $fontStack = "'Instrument Sans', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif";
@endphp
<x-mail.layout>
    <x-slot:preheader>{{ __(':inviter added you to :space.', ['inviter' => $inviterName, 'space' => $spaceName]) }}</x-slot:preheader>

    <h1 style="margin:0 0 16px 0; font-family:{{ $fontStack }}; font-size:24px; font-weight:600; color:{{ $text }}; line-height:1.3;">
        {{ __(':inviter added you to :space.', ['inviter' => $inviterName, 'space' => $spaceName]) }}
    </h1>

    <p style="margin:0 0 16px 0; font-family:{{ $fontStack }}; font-size:15px; line-height:1.6; color:{{ $text }};">
        {{ __('They set you up as') }}
        <strong style="color:{{ $text }}; font-weight:600;">{{ $role }}</strong>.
        {{ __("Jump in whenever — the registry's waiting.") }}
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:28px 0;">
        <tr>
            <td style="background-color:{{ $primary }};">
                <a href="{{ $acceptUrl }}"
                   style="display:inline-block; padding:12px 24px; font-family:{{ $fontStack }}; font-size:15px; font-weight:600; color:#ffffff; text-decoration:none;">
                    {{ __('Accept the invite') }}
                </a>
            </td>
        </tr>
    </table>

    <hr style="border:none; border-top:1px dashed {{ $border }}; margin:24px 0;">

    <p style="margin:0; font-family:{{ $fontStack }}; font-size:13px; line-height:1.6; color:{{ $muted }};">
        {{ __('Not your thing?') }}
        <a href="{{ $declineUrl }}" style="color:{{ $muted }}; text-decoration:underline;">{{ __('Decline instead.') }}</a>
    </p>
</x-mail.layout>
