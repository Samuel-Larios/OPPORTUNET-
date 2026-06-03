@props([
    'title',
    'heading' => null,
    'eyebrow' => 'Opportunet Mondiale',
    'logoSrc' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
</head>
<body style="margin:0; padding:24px; background:#eef7f8; font-family:Arial, sans-serif; color:#17384b;">
    <div style="max-width:680px; margin:0 auto; background:linear-gradient(180deg, #ffffff 0%, #f4fbfb 100%); border-radius:24px; overflow:hidden; border:1px solid #dceceb;">
        <div style="padding:28px 32px 18px; background:linear-gradient(135deg, #0f3947 0%, #17718f 100%); color:#ffffff;">
            @if ($logoSrc)
                <img src="{{ $logoSrc }}" alt="Opportunet Mondiale" width="140" style="display:block; width:140px; max-width:100%; height:auto; margin-bottom:16px;">
            @endif
            <div style="font-size:12px; letter-spacing:0.18em; text-transform:uppercase; opacity:0.82; font-weight:700;">
                {{ $eyebrow }}
            </div>
            <h1 style="margin:10px 0 0; font-size:30px; line-height:1.15; color:#ffffff;">
                {{ $heading ?: $title }}
            </h1>
        </div>

        <div style="padding:32px; line-height:1.7; color:#17384b;">
            {{ $slot }}
        </div>

        <div style="padding:0 32px 28px; color:#5f7681; font-size:14px;">
            <p style="margin:0 0 8px;">Opportunet Mondiale</p>
            <p style="margin:0;">
                <a href="mailto:contact@opportunetmondiale.com" style="color:#17718f; text-decoration:none;">contact@opportunetmondiale.com</a>
            </p>
        </div>
    </div>
</body>
</html>
