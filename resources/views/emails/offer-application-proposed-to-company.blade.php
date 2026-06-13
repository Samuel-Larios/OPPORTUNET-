@php
    $logoFile = public_path('images/logo/imgi_24_cropped-cropped-Logo-OPM-1-300x213.png');
    $logoSrc = isset($message) && file_exists($logoFile)
        ? $message->embed($logoFile)
        : asset('images/logo/imgi_24_cropped-cropped-Logo-OPM-1-300x213.png');
@endphp

<x-email.layout :title="__('offers.application.company_proposal_mail.subject', ['offer' => $application->opportunite->titre])" :logo-src="$logoSrc">
    <p>{{ __('offers.application.company_proposal_mail.greeting', ['name' => $application->prenom]) }}</p>

    <p>{{ __('offers.application.company_proposal_mail.intro', ['offer' => $application->opportunite->titre]) }}</p>

    <div style="margin:24px 0; padding:20px; background:#f5fbfb; border:1px solid #dceceb; border-radius:18px;">
        <p style="margin:0 0 12px;">
            <strong>{{ __('offers.application.company_proposal_mail.company_label') }}</strong><br>
            {{ $application->opportunite->organisation ?: ($application->opportunite->user?->fullName() ?: __('offers.application.company_proposal_mail.company_fallback')) }}
        </p>

        <p style="margin:0;">
            <strong>{{ __('offers.application.company_proposal_mail.status_label') }}</strong><br>
            {{ __('admin.applications.statuses.' . $application->statut) }}
        </p>
    </div>

    @if ($application->notes_admin)
        <div style="margin:24px 0; padding:20px; background:#fff8ef; border:1px solid #f0dec4; border-radius:18px;">
            <p style="margin:0;">
                <strong>{{ __('offers.application.company_proposal_mail.note_label') }}</strong><br>
                {{ $application->notes_admin }}
            </p>
        </div>
    @endif

    <p>{{ __('offers.application.company_proposal_mail.closing') }}</p>
</x-email.layout>
