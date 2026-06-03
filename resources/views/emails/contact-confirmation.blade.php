@php
    $logoFile = public_path('images/logo/imgi_24_cropped-cropped-Logo-OPM-1-300x213.png');
    $logoSrc = isset($message) && file_exists($logoFile)
        ? $message->embed($logoFile)
        : asset('images/logo/imgi_24_cropped-cropped-Logo-OPM-1-300x213.png');
@endphp

<x-email.layout :title="__('home.forms.contact.mail.user_intro')" :logo-src="$logoSrc">
    <p>{{ __('home.forms.contact.mail.user_greeting', ['name' => $contact->prenom]) }}</p>

    <p>{{ __('home.forms.contact.mail.user_intro') }}</p>

    <div style="margin:24px 0; padding:20px; background:#f5fbfb; border:1px solid #dceceb; border-radius:18px;">
        <p style="margin:0 0 12px;">
            <strong>{{ __('home.forms.contact.mail.subject_label') }}</strong><br>
            {{ $contact->subjectLabel() }}
        </p>

        @if ($contact->sujet === 'autre' && $contact->sujet_personnalise)
            <p style="margin:0 0 12px;">
                <strong>{{ __('home.forms.contact.mail.details_label') }}</strong><br>
                {{ $contact->sujet_personnalise }}
            </p>
        @endif

        <p style="margin:0;">
            <strong>{{ __('home.forms.contact.mail.message_label') }}</strong><br>
            {{ $contact->message }}
        </p>
    </div>

    <p>{{ __('home.forms.contact.mail.user_outro') }}</p>
    <p>{{ __('home.forms.contact.mail.signature') }}</p>
</x-email.layout>
