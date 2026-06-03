@php
    $logoFile = public_path('images/logo/imgi_24_cropped-cropped-Logo-OPM-1-300x213.png');
    $logoSrc = isset($message) && file_exists($logoFile)
        ? $message->embed($logoFile)
        : asset('images/logo/imgi_24_cropped-cropped-Logo-OPM-1-300x213.png');
@endphp

<x-email.layout :title="__('offers.application.company_mail.subject', ['offer' => $application->opportunite->titre])" :logo-src="$logoSrc">
    <p>{{ __('offers.application.company_mail.greeting', ['name' => $application->opportunite->user?->fullName() ?: __('offers.application.company_mail.company_fallback')]) }}</p>

    <p>{{ __('offers.application.company_mail.intro', ['offer' => $application->opportunite->titre]) }}</p>

    <div style="margin:24px 0; padding:20px; background:#f5fbfb; border:1px solid #dceceb; border-radius:18px;">
        <p style="margin:0 0 12px;">
            <strong>{{ __('offers.application.company_mail.candidate_label') }}</strong><br>
            {{ trim($application->prenom . ' ' . $application->nom) }}
        </p>

        <p style="margin:0;">
            <strong>{{ __('offers.application.company_mail.status_label') }}</strong><br>
            {{ __('admin.applications.statuses.' . $application->statut) }}
        </p>
    </div>

    @if ($application->notes_admin)
        <div style="margin:24px 0; padding:20px; background:#fff8ef; border:1px solid #f0dec4; border-radius:18px;">
            <p style="margin:0;">
                <strong>{{ __('offers.application.company_mail.note_label') }}</strong><br>
                {{ $application->notes_admin }}
            </p>
        </div>
    @endif

    <p style="margin:28px 0;">
        <a href="{{ route('panel.company.applications') }}" style="display:inline-block; padding:14px 24px; border-radius:999px; background:linear-gradient(135deg, #17718f 0%, #2f8ba3 100%); color:#ffffff; text-decoration:none; font-weight:700;">
            {{ __('offers.application.company_mail.cta') }}
        </a>
    </p>

    <p>{{ __('offers.application.company_mail.closing') }}</p>
</x-email.layout>
