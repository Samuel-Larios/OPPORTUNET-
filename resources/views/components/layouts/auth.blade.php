@props([
    'title' => 'Connexion',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title }} | Opportunet Mondiale</title>
    <meta name="robots" content="noindex,nofollow" />
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
                <span class="section-label">{{ __('admin.brand.label') }}</span>
                <h1>{{ $title }}</h1>
                <p>{{ __('admin.brand.subtitle') }}</p>
            </div>

            {{ $slot }}
        </section>
    </main>
</body>
</html>
