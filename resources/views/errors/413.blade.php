@php
    $tooLargeTitle = app()->getLocale() === 'fr'
        ? 'Candidature trop volumineuse'
        : 'Application too large';
    $tooLargeMessage = app()->getLocale() === 'fr'
        ? 'Les fichiers envoyes sont trop volumineux. Gardez chaque fichier sous 5 Mo et le total de la candidature sous 64 Mo.'
        : 'The uploaded files are too large. Keep each file under 5 MB and the full application under 64 MB.';
    $tooLargeHint = app()->getLocale() === 'fr'
        ? 'Reduisez la taille ou le nombre de fichiers, puis reessayez.'
        : 'Reduce the file size or the number of files, then try again.';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>413 | Opportunet Mondiale</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,300&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('templatemo-622-clearwave.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin-panel.css') }}" />
</head>
<body class="panel-auth-body">
    <main class="panel-auth-shell">
        <section class="panel-auth-card">
            <a href="{{ route('home') }}" class="panel-auth-brand">
                <img src="{{ asset('images/logo/imgi_27_cropped-cropped-Logo-OPM-1-600x427.png') }}" alt="Opportunet Mondiale" />
                <div>
                    <span>{{ __('admin.brand.kicker') }}</span>
                    <strong>Opportunet Mondiale</strong>
                </div>
            </a>

            <div class="panel-auth-copy">
                <span class="section-label">413</span>
                <h1>{{ $tooLargeTitle }}</h1>
                <p>{{ $tooLargeMessage }}</p>
            </div>

            <div class="panel-auth-alt">
                <p>{{ $tooLargeHint }}</p>
            </div>

            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('offers.index') }}" class="panel-primary-btn">
                {{ __('offers.detail.back') }}
            </a>
        </section>
    </main>
</body>
</html>
