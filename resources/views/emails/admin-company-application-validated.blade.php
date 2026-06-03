@php
    $logoFile = public_path('images/logo/imgi_24_cropped-cropped-Logo-OPM-1-300x213.png');
    $logoSrc = isset($message) && file_exists($logoFile)
        ? $message->embed($logoFile)
        : asset('images/logo/imgi_24_cropped-cropped-Logo-OPM-1-300x213.png');
@endphp

<x-email.layout :title="__('offers.application.admin_company_validation_mail.subject', ['offer' => $application->opportunite->titre])" :logo-src="$logoSrc">
    <p>{{ __('offers.application.admin_company_validation_mail.greeting') }}</p>

    <p>{{ __('offers.application.admin_company_validation_mail.intro', ['offer' => $application->opportunite->titre]) }}</p>

    <div style="margin:24px 0; padding:20px; background:#f5fbfb; border:1px solid #dceceb; border-radius:18px;">
        <p style="margin:0 0 12px;">
            <strong>{{ __('offers.application.admin_company_validation_mail.company_label') }}</strong><br>
            {{ $application->opportunite->organisation ?: ($application->opportunite->user?->fullName() ?: __('offers.application.admin_company_validation_mail.company_fallback')) }}
        </p>

        <p style="margin:0 0 12px;">
            <strong>{{ __('offers.application.admin_company_validation_mail.candidate_label') }}</strong><br>
            {{ trim($application->prenom . ' ' . $application->nom) }}
        </p>

        <p style="margin:0;">
            <strong>{{ __('offers.application.admin_company_validation_mail.status_label') }}</strong><br>
            {{ __('admin.applications.statuses.' . $application->statut) }}
        </p>
    </div>

    @if ($application->note_entreprise)
        <div style="margin:24px 0; padding:20px; background:#fff8ef; border:1px solid #f0dec4; border-radius:18px;">
            <p style="margin:0;">
                <strong>{{ __('offers.application.admin_company_validation_mail.note_label') }}</strong><br>
                {{ $application->note_entreprise }}
            </p>
        </div>
    @endif

    <p style="margin:28px 0;">
        <a href="{{ route('panel.admin.applications') }}" style="display:inline-block; padding:14px 24px; border-radius:999px; background:linear-gradient(135deg, #17718f 0%, #2f8ba3 100%); color:#ffffff; text-decoration:none; font-weight:700;">
            {{ __('offers.application.admin_company_validation_mail.cta') }}
        </a>
    </p>

    <p>{{ __('offers.application.admin_company_validation_mail.closing') }}</p>
</x-email.layout>
