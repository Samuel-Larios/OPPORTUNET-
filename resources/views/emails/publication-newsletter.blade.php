@php
    $logoFile = public_path('images/logo/imgi_24_cropped-cropped-Logo-OPM-1-300x213.png');
    $logoSrc = isset($message) && file_exists($logoFile)
        ? $message->embed($logoFile)
        : asset('images/logo/imgi_24_cropped-cropped-Logo-OPM-1-300x213.png');
@endphp

<x-email.layout :title="$payload['subject']" :heading="$payload['subject']" :logo-src="$logoSrc">
    @if (! empty($payload['recipient_name']))
        <p>Bonjour {{ $payload['recipient_name'] }},</p>
    @else
        <p>Bonjour,</p>
    @endif

    <p>Un nouveau contenu vient d'être publié sur Opportunet Mondiale.</p>

    <div style="margin:24px 0; padding:20px; background:#f5fbfb; border:1px solid #dceceb; border-radius:18px;">
        <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#6a7488;">{{ $payload['label'] }}</div>
        <h2 style="margin:10px 0 12px; font-size:24px; line-height:1.25; color:#172033;">{{ $payload['title'] }}</h2>
        <p style="margin:0; color:#3e4658;">{{ $payload['summary'] }}</p>
    </div>

    <p>Cliquez ci-dessous pour consulter la publication :</p>

    <p style="margin:28px 0;">
        <a href="{{ $payload['url'] }}" style="display:inline-block; padding:14px 24px; border-radius:999px; background:linear-gradient(135deg, #17718f 0%, #2f8ba3 100%); color:#ffffff; text-decoration:none; font-weight:700;">
            Voir la publication
        </a>
    </p>

    <p style="margin-bottom:0; color:#6a7488; font-size:14px;">
        Vous recevez cet email parce que vous êtes abonné à la newsletter ou parce que vous avez un compte actif sur la plateforme.
    </p>
</x-email.layout>
