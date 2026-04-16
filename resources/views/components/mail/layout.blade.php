@php
    $bg = '#27272a';
    $panel = '#18181b';
    $border = '#52525b';
    $text = '#e4e4e7';
    $muted = '#a1a1aa';
    $primary = '#2563eb';
    $fontStack = "'Instrument Sans', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif";
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark">
    <meta name="supported-color-schemes" content="dark">
    <title>{{ config('app.name') }}</title>
</head>
<body style="margin:0; padding:0; background-color:{{ $bg }}; color:{{ $text }}; font-family:{{ $fontStack }};">
    <div style="display:none; font-size:1px; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden; mso-hide:all; color:transparent; visibility:hidden;">
        {{ $preheader ?? '' }}
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:{{ $bg }};">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0" border="0"
                       style="width:100%; max-width:560px;">

                    <tr>
                        <td style="padding:0 0 24px 0;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding-right:10px; vertical-align:middle;">
                                        <img src="{{ url('/favicon/android-chrome-192x192.png') }}"
                                             alt=""
                                             width="24" height="24"
                                             style="display:block; width:24px; height:24px; border:0;">
                                    </td>
                                    <td style="vertical-align:middle;">
                                        <span style="font-family:{{ $fontStack }}; font-size:20px; font-weight:600; letter-spacing:0.02em; color:{{ $text }};">
                                            {{ config('app.name') }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color:{{ $panel }}; border:1px dashed {{ $border }}; padding:40px;">
                            {{ $slot }}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:24px 0 0 0; font-family:{{ $fontStack }}; font-size:12px; color:{{ $muted }};">
                            © {{ date('Y') }} {{ config('app.name') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
