@props([
    'title' => 'Panel',
])

@php
    $user = auth()->user();
    $siteSettings = \App\Models\ParametreSite::query()
        ->whereIn('cle', ['site_email', 'site_horaires', 'site_adresse'])
        ->get()
        ->keyBy('cle');
    $siteEmail = $siteSettings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com';
    $siteHours = $siteSettings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00';
    $siteAddress = $siteSettings->get('site_adresse')?->valeur ?? 'En face de la Mairie de Missérété, Ouémé, BJ';
    $unreadNotificationsCount = $user?->unreadNotifications()->count() ?? 0;
    $menuBadges = [
        'panel.notifications' => $unreadNotificationsCount,
    ];

    if ($user?->isAdmin()) {
        $menuBadges = array_merge($menuBadges, [
            'panel.editor.offers' => \App\Models\Opportunite::query()->where('statut', 'en_attente_validation')->count(),
            'panel.admin.applications' => \App\Models\CandidatureOffre::query()->where('statut', 'en_attente')->count(),
            'panel.admin.contacts' => \App\Models\Contact::query()->where('statut', 'non_lu')->count(),
            'panel.admin.cv-depots' => \App\Models\CvDepot::query()->where('statut', 'nouveau')->count(),
            'panel.admin.training-registrations' => \App\Models\InscriptionFormation::query()->where('statut', 'en_attente')->count(),
            'panel.admin.article-comments' => \App\Models\BlogCommentaire::query()->where('statut', 'en_attente')->count(),
            'panel.admin.testimonials' => \App\Models\Temoignage::query()->where('statut', 'en_attente')->count(),
            'panel.admin.prayers' => \App\Models\MurDePriere::query()->where('statut', 'en_attente')->count(),
            'panel.admin.security' => \Illuminate\Support\Facades\Schema::hasTable('security_ip_blocks')
                ? \App\Models\SecurityIpBlock::query()
                    ->where(function ($query) {
                        $query->where('is_manual', true)
                            ->orWhereNull('blocked_until')
                            ->orWhere('blocked_until', '>', now());
                    })
                    ->count()
                : 0,
        ]);
    } elseif ($user?->canManageOffers()) {
        $menuBadges['panel.editor.offers'] = \App\Models\Opportunite::query()->where('statut', 'en_attente_validation')->count();
    } elseif ($user?->isCompany()) {
        $menuBadges = array_merge($menuBadges, [
            'panel.company.offers' => \App\Models\Opportunite::query()
                ->where('user_id', $user->id)
                ->where('statut', 'en_attente_validation')
                ->count(),
            'panel.company.applications' => \App\Models\CandidatureOffre::query()
                ->whereHas('opportunite', fn ($query) => $query->where('user_id', $user->id))
                ->where('statut', 'proposee_entreprise')
                ->count(),
        ]);
    } elseif ($user) {
        $menuBadges['panel.user.applications'] = \App\Models\CandidatureOffre::query()
            ->visibleToUser($user)
            ->where('statut', 'informations_complementaires')
            ->count();
    }

    $baseNotificationsItem = [
        'label' => __('admin.nav.notifications'),
        'route' => 'panel.notifications',
        'visible' => (bool) $user,
        'badge' => $menuBadges['panel.notifications'] ?? 0,
    ];

    if ($user?->isAdmin()) {
        $menuGroups = collect([
            [
                'label' => __('admin.nav_groups.overview'),
                'items' => [
                    $baseNotificationsItem,
                    [
                        'label' => __('admin.nav.dashboard'),
                        'route' => 'panel.admin.dashboard',
                        'visible' => true,
                    ],
                ],
            ],
            [
                'label' => __('admin.nav_groups.publication'),
                'items' => [
                    ['label' => __('admin.nav.offers'), 'route' => 'panel.editor.offers', 'visible' => true, 'badge' => $menuBadges['panel.editor.offers'] ?? 0],
                    ['label' => __('admin.nav.articles'), 'route' => 'panel.editor.articles', 'visible' => true],
                    ['label' => __('admin.nav.services'), 'route' => 'panel.editor.services', 'visible' => true],
                    ['label' => __('admin.nav.trainings'), 'route' => 'panel.editor.trainings', 'visible' => true],
                    ['label' => __('admin.nav.categories'), 'route' => 'panel.editor.categories', 'visible' => true],
                    ['label' => __('admin.nav.verses'), 'route' => 'panel.editor.verses', 'visible' => true],
                    ['label' => app()->getLocale() === 'fr' ? 'Pensées du jour' : 'Thoughts of the day', 'route' => 'panel.editor.thoughts', 'visible' => true],
                    ['label' => app()->getLocale() === 'fr' ? 'Exhortations' : 'Exhortations', 'route' => 'panel.editor.exhortations', 'visible' => true],
                    ['label' => app()->getLocale() === 'fr' ? 'Prières du jour' : 'Daily prayers', 'route' => 'panel.editor.daily-prayers', 'visible' => true],
                ],
            ],
            [
                'label' => __('admin.nav_groups.follow_up'),
                'items' => [
                    ['label' => __('admin.nav.applications'), 'route' => 'panel.admin.applications', 'visible' => true, 'badge' => $menuBadges['panel.admin.applications'] ?? 0],
                    ['label' => __('admin.nav.contacts'), 'route' => 'panel.admin.contacts', 'visible' => true, 'badge' => $menuBadges['panel.admin.contacts'] ?? 0],
                    ['label' => __('admin.nav.cv_depots'), 'route' => 'panel.admin.cv-depots', 'visible' => true, 'badge' => $menuBadges['panel.admin.cv-depots'] ?? 0],
                    ['label' => __('admin.nav.training_registrations'), 'route' => 'panel.admin.training-registrations', 'visible' => true, 'badge' => $menuBadges['panel.admin.training-registrations'] ?? 0],
                ],
            ],
            [
                'label' => __('admin.nav_groups.community'),
                'items' => [
                    ['label' => __('admin.nav.article_comments'), 'route' => 'panel.admin.article-comments', 'visible' => true, 'badge' => $menuBadges['panel.admin.article-comments'] ?? 0],
                    ['label' => __('admin.nav.testimonials'), 'route' => 'panel.admin.testimonials', 'visible' => true, 'badge' => $menuBadges['panel.admin.testimonials'] ?? 0],
                    ['label' => __('admin.nav.prayers'), 'route' => 'panel.admin.prayers', 'visible' => true, 'badge' => $menuBadges['panel.admin.prayers'] ?? 0],
                ],
            ],
            [
                'label' => __('admin.nav_groups.admin'),
                'items' => [
                    ['label' => __('admin.nav.users'), 'route' => 'panel.admin.users', 'visible' => true],
                    ['label' => __('admin.nav.settings'), 'route' => 'panel.admin.settings', 'visible' => true],
                    ['label' => __('admin.nav_security'), 'route' => 'panel.admin.security', 'visible' => true, 'badge' => $menuBadges['panel.admin.security'] ?? 0],
                ],
            ],
        ]);
    } elseif ($user?->canManageOffers()) {
        $menuGroups = collect([
            [
                'label' => __('admin.nav_groups.overview'),
                'items' => [
                    $baseNotificationsItem,
                ],
            ],
            [
                'label' => __('admin.nav_groups.publication'),
                'items' => [
                    ['label' => __('admin.nav.offers'), 'route' => 'panel.editor.offers', 'visible' => true, 'badge' => $menuBadges['panel.editor.offers'] ?? 0],
                    ['label' => __('admin.nav.articles'), 'route' => 'panel.editor.articles', 'visible' => true],
                    ['label' => __('admin.nav.services'), 'route' => 'panel.editor.services', 'visible' => true],
                    ['label' => __('admin.nav.trainings'), 'route' => 'panel.editor.trainings', 'visible' => true],
                    ['label' => __('admin.nav.categories'), 'route' => 'panel.editor.categories', 'visible' => true],
                    ['label' => __('admin.nav.verses'), 'route' => 'panel.editor.verses', 'visible' => true],
                    ['label' => app()->getLocale() === 'fr' ? 'Pensées du jour' : 'Thoughts of the day', 'route' => 'panel.editor.thoughts', 'visible' => true],
                    ['label' => app()->getLocale() === 'fr' ? 'Exhortations' : 'Exhortations', 'route' => 'panel.editor.exhortations', 'visible' => true],
                    ['label' => app()->getLocale() === 'fr' ? 'Prières du jour' : 'Daily prayers', 'route' => 'panel.editor.daily-prayers', 'visible' => true],
                ],
            ],
        ]);
    } elseif ($user?->isCompany()) {
        $menuGroups = collect([
            [
                'label' => __('admin.nav_groups.overview'),
                'items' => [
                    $baseNotificationsItem,
                    ['label' => __('admin.nav.company_dashboard'), 'route' => 'panel.company.dashboard', 'visible' => true],
                ],
            ],
            [
                'label' => __('admin.nav_groups.company'),
                'items' => [
                    ['label' => __('admin.nav.company_offers'), 'route' => 'panel.company.offers', 'visible' => true, 'badge' => $menuBadges['panel.company.offers'] ?? 0],
                    ['label' => __('admin.nav.company_applications'), 'route' => 'panel.company.applications', 'visible' => true, 'badge' => $menuBadges['panel.company.applications'] ?? 0],
                ],
            ],
        ]);
    } else {
        $menuGroups = collect([
            [
                'label' => __('admin.nav_groups.overview'),
                'items' => [
                    $baseNotificationsItem,
                    ['label' => __('admin.nav.user_dashboard'), 'route' => 'panel.user.dashboard', 'visible' => true],
                ],
            ],
            [
                'label' => __('admin.nav_groups.personal'),
                'items' => [
                    ['label' => __('admin.nav.requests'), 'route' => 'panel.user.requests', 'visible' => true],
                    ['label' => __('admin.nav.my_applications'), 'route' => 'panel.user.applications', 'visible' => true, 'badge' => $menuBadges['panel.user.applications'] ?? 0],
                    ['label' => __('admin.nav.my_cv_depots'), 'route' => 'panel.user.cv-depots', 'visible' => true],
                    ['label' => __('admin.nav.my_trainings'), 'route' => 'panel.user.trainings', 'visible' => true],
                ],
            ],
            [
                'label' => __('admin.nav_groups.community'),
                'items' => [
                    ['label' => __('admin.nav.my_testimonials'), 'route' => 'panel.user.testimonials', 'visible' => true],
                    ['label' => __('admin.nav.my_prayers'), 'route' => 'panel.user.prayers', 'visible' => true],
                ],
            ],
        ]);
    }

    $menuGroups = $menuGroups->map(function ($group) {
        $group['items'] = collect($group['items'])
            ->filter(fn ($item) => $item['visible'])
            ->values();

        return $group;
    })->filter(fn ($group) => $group['items']->isNotEmpty())->values();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title }} | Opportunet Mondiale</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,300&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('templatemo-622-clearwave.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin-panel.css') }}" />
    @livewireStyles
</head>
<body class="panel-body">
    <input type="checkbox" id="panel-nav-toggle" class="panel-nav-toggle" />

    <div class="panel-layout">
        <aside class="panel-sidebar">
            <div class="panel-sidebar-mobile-head">
                <label for="panel-nav-toggle" class="panel-mobile-close" aria-label="{{ app()->getLocale() === 'fr' ? 'Fermer le menu' : 'Close menu' }}">
                    <span aria-hidden="true">&times;</span>
                </label>
            </div>

            <a href="{{ route('dashboard') }}" class="panel-brand">
                <img src="{{ asset('images/logo/imgi_27_cropped-cropped-Logo-OPM-1-600x427.png') }}" alt="Opportunet Mondiale" />
                <div>
                    <span>{{ __('admin.brand.kicker') }}</span>
                    <strong>Opportunet Mondiale</strong>
                </div>
            </a>

            <div class="panel-user-card">
                <strong>{{ $user?->fullName() }}</strong>
                <span>{{ $user?->role?->libelle ?? __('admin.roles.user') }}</span>
            </div>

            <div class="panel-sidebar-navstack">
                <nav class="panel-menu">
                    @foreach ($menuGroups as $group)
                        <section class="panel-menu-group">
                            <div class="panel-menu-group-title">{{ $group['label'] }}</div>
                            <div class="panel-menu-group-links">
                                @foreach ($group['items'] as $item)
                                    <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['route']) ? 'is-active' : '' }}">
                                        <span>{{ $item['label'] }}</span>
                                        @if (($item['badge'] ?? 0) > 0)
                                            <span class="panel-nav-badge">{{ $item['badge'] > 99 ? '99+' : $item['badge'] }}</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </nav>

                <div class="panel-sidebar-footer">
                    <a href="{{ route('home') }}" class="panel-secondary-link">{{ __('admin.nav.back_site') }}</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="panel-logout">{{ __('admin.nav.logout') }}</button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="panel-main">
            <div class="panel-mobile-bar">
                <label for="panel-nav-toggle" class="panel-mobile-toggle" aria-label="{{ app()->getLocale() === 'fr' ? 'Ouvrir le menu' : 'Open menu' }}">
                    <span class="panel-mobile-toggle-icon" aria-hidden="true"></span>
                    <span>{{ app()->getLocale() === 'fr' ? 'Menu admin' : 'Admin menu' }}</span>
                </label>

                <a href="{{ route('panel.notifications') }}" class="panel-mobile-notifications{{ request()->routeIs('panel.notifications') ? ' is-active' : '' }}">
                    {{ __('admin.nav.notifications') }}
                    @if ($unreadNotificationsCount > 0)
                        <span class="panel-nav-badge">{{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}</span>
                    @endif
                </a>
            </div>

            <div class="site-topbar panel-site-topbar">
                <div class="container site-topbar-inner">
                    <div class="site-topbar-left">
                        <a href="mailto:{{ $siteEmail }}" class="topbar-link">{{ __('home.topbar.email') }}: {{ $siteEmail }}</a>
                        <span class="topbar-link topbar-hours">{{ __('home.topbar.hours') }}: {{ $siteHours }}</span>
                    </div>
                    <div class="site-topbar-right">
                        <span class="topbar-link topbar-location panel-site-address">{{ $siteAddress }}</span>
                        <div class="topbar-locale-switcher" aria-label="{{ __('home.nav.language_switcher') }}">
                            <a href="{{ route('locale.switch', ['locale' => 'fr']) }}"
                                class="locale-flag-link{{ app()->getLocale() === 'fr' ? ' active' : '' }}"
                                aria-label="{{ __('home.nav.switch_to_french') }}">
                                <span class="flag-icon flag-fr" aria-hidden="true"></span>
                                <span>FR</span>
                            </a>
                            <a href="{{ route('locale.switch', ['locale' => 'en']) }}"
                                class="locale-flag-link{{ app()->getLocale() === 'en' ? ' active' : '' }}"
                                aria-label="{{ __('home.nav.switch_to_english') }}">
                                <span class="flag-icon flag-en" aria-hidden="true"></span>
                                <span>EN</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-main-body">
                <header class="panel-header">
                    <div>
                        <span class="section-label">{{ __('admin.header.label') }}</span>
                        <h1>{{ $title }}</h1>
                    </div>
                    <div class="panel-header-meta">
                        <span>{{ __('admin.header.connected_as') }}</span>
                        <strong>{{ $user?->role?->libelle ?? __('admin.roles.user') }}</strong>
                        <a href="{{ route('panel.notifications') }}" class="panel-header-notifications{{ request()->routeIs('panel.notifications') ? ' is-active' : '' }}">
                            {{ __('admin.nav.notifications') }}
                            @if ($unreadNotificationsCount > 0)
                                <span class="panel-nav-badge">{{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}</span>
                            @endif
                        </a>
                    </div>
                </header>

                <main class="panel-content">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <label for="panel-nav-toggle" class="panel-sidebar-scrim" aria-hidden="true"></label>
    </div>

    @livewireScripts
</body>
</html>
