@php
    $training = $registration->formation;
    $logoFile = public_path('images/logo/imgi_24_cropped-cropped-Logo-OPM-1-300x213.png');
    $logoSrc = isset($message) && file_exists($logoFile)
        ? $message->embed($logoFile)
        : asset('images/logo/imgi_24_cropped-cropped-Logo-OPM-1-300x213.png');
@endphp

<x-email.layout :title="'Nouvelle inscription formation - ' . ($training?->titre ?? 'Formation')" heading="Nouvelle inscription formation" :logo-src="$logoSrc">
    <p>Une nouvelle inscription à une formation a été envoyée depuis la plateforme.</p>

    <div style="margin:24px 0; padding:20px; background:#f5fbfb; border:1px solid #dceceb; border-radius:18px;">
        <p style="margin:0 0 10px;"><strong>Formation :</strong> {{ $training?->titre ?? 'Formation' }}</p>
        <p style="margin:0 0 10px;"><strong>Nom :</strong> {{ $registration->prenom }} {{ $registration->nom }}</p>
        <p style="margin:0 0 10px;"><strong>Email :</strong> {{ $registration->email }}</p>
        <p style="margin:0 0 10px;"><strong>Téléphone :</strong> {{ $registration->telephone ?: '-' }}</p>
        <p style="margin:0 0 10px;"><strong>WhatsApp :</strong> {{ $registration->whatsapp ?: '-' }}</p>
        <p style="margin:0 0 10px;"><strong>Pays :</strong> {{ $registration->pays ?: '-' }}</p>
        <p style="margin:0;"><strong>Profession :</strong> {{ $registration->profession ?: '-' }}</p>
    </div>

    @if ($registration->motivation)
        <div style="margin:24px 0; padding:20px; background:#fff8ef; border:1px solid #f0dec4; border-radius:18px;">
            <p style="margin:0;"><strong>Motivation :</strong><br>{{ $registration->motivation }}</p>
        </div>
    @endif

    <p>Consultez l espace admin pour traiter cette inscription.</p>
</x-email.layout>
