@php
    $logoFile = public_path('images/logo/imgi_24_cropped-cropped-Logo-OPM-1-300x213.png');
    $logoSrc = isset($message) && file_exists($logoFile)
        ? $message->embed($logoFile)
        : asset('images/logo/imgi_24_cropped-cropped-Logo-OPM-1-300x213.png');
@endphp

<x-email.layout :title="__('admin.auth.verify_mail.subject')" :heading="__('admin.auth.verify_title')" :logo-src="$logoSrc">
    <p>{{ __('admin.auth.verify_mail.greeting', ['name' => $user->fullName()]) }}</p>

    <p>{{ __('admin.auth.verify_mail.intro') }}</p>

    <p style="margin:28px 0;">
        <a href="{{ $verificationUrl }}" style="display:inline-block; padding:14px 24px; border-radius:999px; background:linear-gradient(135deg, #17718f 0%, #2f8ba3 100%); color:#ffffff; text-decoration:none; font-weight:700;">
            {{ __('admin.auth.verify_mail.button') }}
        </a>
    </p>

    <p>{{ __('admin.auth.verify_mail.fallback') }}</p>

    <p style="word-break:break-all; color:#2f6075;">
        <a href="{{ $verificationUrl }}" style="color:#17718f;">{{ $verificationUrl }}</a>
    </p>

    <p>{{ __('admin.auth.verify_mail.expire') }}</p>
    <p>{{ __('admin.auth.verify_mail.ignore') }}</p>
    <p>{{ __('admin.auth.verify_mail.closing') }}</p>
</x-email.layout>
