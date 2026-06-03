@php
    $logoFile = public_path('images/logo/imgi_24_cropped-cropped-Logo-OPM-1-300x213.png');
    $logoSrc = isset($message) && file_exists($logoFile)
        ? $message->embed($logoFile)
        : asset('images/logo/imgi_24_cropped-cropped-Logo-OPM-1-300x213.png');
@endphp

<x-email.layout title="Nouveau message de contact" heading="Nouveau message de contact" :logo-src="$logoSrc">
    <p>Un nouveau message de contact a été reçu depuis le site.</p>

    <div style="margin:24px 0; padding:20px; background:#f5fbfb; border:1px solid #dceceb; border-radius:18px;">
        <p style="margin:0 0 10px;"><strong>Nom :</strong> {{ $contact->fullName() }}</p>
        <p style="margin:0 0 10px;"><strong>Email :</strong> {{ $contact->email }}</p>
        <p style="margin:0 0 10px;"><strong>Téléphone :</strong> {{ $contact->telephone ?: '-' }}</p>
        <p style="margin:0 0 10px;"><strong>WhatsApp :</strong> {{ $contact->whatsapp ?: '-' }}</p>
        <p style="margin:0 0 10px;"><strong>Pays :</strong> {{ $contact->pays ?: '-' }}</p>
        <p style="margin:0 0 10px;"><strong>Sujet :</strong> {{ $contact->subjectLabel() }}</p>

        @if ($contact->sujet === 'autre' && $contact->sujet_personnalise)
            <p style="margin:0 0 10px;"><strong>Precisions :</strong> {{ $contact->sujet_personnalise }}</p>
        @endif

        <p style="margin:0 0 10px;"><strong>Message :</strong><br>{{ $contact->message }}</p>
        <p style="margin:0;"><strong>Envoyé le :</strong> {{ $contact->created_at?->format('d/m/Y H:i') }}</p>
    </div>

    <p style="margin:28px 0;">
        <a href="{{ route('panel.admin.contacts') }}" style="display:inline-block; padding:14px 24px; border-radius:999px; background:linear-gradient(135deg, #17718f 0%, #2f8ba3 100%); color:#ffffff; text-decoration:none; font-weight:700;">
            Ouvrir les contacts admin
        </a>
    </p>
</x-email.layout>
