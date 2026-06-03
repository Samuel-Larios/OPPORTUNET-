@props([
    'title' => null,
    'siteName' => 'Opportunet Mondiale',
    'siteSlogan' => null,
    'siteEmail' => 'contact@opportunetmondiale.com',
    'siteHours' => 'Lundi - Samedi 08:00 - 22:00',
    'siteAddress' => 'En face de la Mairie de Missérété, Ouémé, BJ',
    'siteWhatsapp' => '+229XXXXXXXXX',
    'siteWhatsappMessage' => null,
    'banner' => null,
    'headerVerses' => null,
    'featuredVerse' => null,
    'showHero' => true,
])

@php
    $homeUrl = route('home');
    $offersUrl = route('offers.index');
    $cvServicesUrl = route('cv.services.index');
    $trainingsUrl = route('trainings.index');
    $articlesUrl = route('articles.index');
    $contactPrayerUrl = route('contact.prayer.index');
    $whatsappHref = 'https://wa.me/' . preg_replace('/\D+/', '', $siteWhatsapp);
    $whatsappCtaHref = $siteWhatsappMessage
        ? $whatsappHref . '?text=' . urlencode($siteWhatsappMessage)
        : $whatsappHref;
    $headerVerses = collect($headerVerses ?? [])->take(2)->values();
    $headerVerseOne = $headerVerses->get(0);
    $headerVerseTwo = $headerVerses->get(1);
    $heroSecondaryHref =
        $banner?->bouton2_style === 'whatsapp'
            ? $whatsappCtaHref
            : ($banner?->bouton2_lien ?:
            $homeUrl . '#home-contact');
    $navItems = [
        [
            'label' => __('home.nav.app'),
            'href' => $homeUrl,
            'active' => request()->routeIs('home'),
        ],
        [
            'label' => __('home.nav.features'),
            'href' => $offersUrl,
            'active' => request()->routeIs('offers.index'),
        ],
        [
            'label' => __('home.nav.services'),
            'href' => $cvServicesUrl,
            'active' => request()->routeIs('cv.services.index'),
        ],
        [
            'label' => __('home.nav.trainings'),
            'href' => $trainingsUrl,
            'active' => request()->routeIs('trainings.index'),
        ],
        [
            'label' => __('home.nav.blog'),
            'href' => $articlesUrl,
            'active' => request()->routeIs('articles.index') || request()->routeIs('articles.show'),
        ],
        [
            'label' => __('home.nav.contact_page'),
            'href' => $contactPrayerUrl,
            'active' => request()->routeIs('contact.prayer.index'),
        ],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ? $title . ' | ' . $siteName : $siteName }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,300&family=Playfair+Display:ital,wght@0,700;1,600&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('templatemo-622-clearwave.css') }}" />
    @livewireStyles

    <link rel="icon" type="image/png" href="{{ asset('images/logo/imgi_4_LE-TRANSFIGURANT-2.png') }}" />
    <link rel="apple-touch-icon" type="image/png" href="{{ asset('images/logo/imgi_4_LE-TRANSFIGURANT-2.png') }}" />
</head>

<body>
    <div class="site-topbar">
        <div class="container site-topbar-inner">
            <div class="site-topbar-left">
                <a href="mailto:{{ $siteEmail }}" class="topbar-link">{{ __('home.topbar.email') }}:
                    {{ $siteEmail }}</a>
                <span class="topbar-link">{{ __('home.topbar.hours') }}: {{ $siteHours }}</span>
            </div>
            <div class="site-topbar-right">
                <span class="topbar-locale-text">{{ __('home.nav.language') }}</span>
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

    <div class="mobile-menu" id="mobileMenu" role="dialog" aria-modal="true"
        aria-label="{{ __('home.mobile_menu.label') }}">
        <div class="mobile-menu-panel">
            <div class="mobile-menu-top">
                <a href="{{ $homeUrl }}" class="mobile-brand" aria-label="{{ __('home.nav.brand') }}">
                    <img src="{{ asset('images/logo/imgi_27_cropped-cropped-Logo-OPM-1-600x427.png') }}"
                        alt="{{ __('home.nav.brand') }}" />
                    <div class="mobile-brand-copy">
                        <span>{{ __('home.nav.kicker') }}</span>
                        <strong>{{ $siteName }}</strong>
                    </div>
                </a>
            </div>
            <p class="mobile-menu-summary">{{ __('home.topbar.focus') }}</p>
            <div class="mobile-menu-links">
                @foreach ($navItems as $item)
                    <a href="{{ $item['href'] }}" class="{{ $item['active'] ? 'is-active' : '' }}"
                        @if ($item['active']) aria-current="page" @endif>{{ $item['label'] }}</a>
                @endforeach
            </div>
            <div class="mobile-menu-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-ghost mobile-secondary">{{ __('admin.nav.user_dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost mobile-secondary">{{ __('admin.auth.login_submit') }}</a>
                @endauth
                <a href="{{ $whatsappCtaHref }}" class="mobile-cta btn-primary" target="_blank"
                    rel="noopener">{{ __('home.nav.start_trial') }}</a>
            </div>
        </div>
    </div>

    <nav class="nav" id="mainNav" role="navigation" aria-label="{{ __('home.nav.label') }}">
        <div class="nav-shell">
            <div class="nav-inner">
                <div class="nav-brand-wrap">
                    <a href="{{ $homeUrl }}" class="nav-logo" aria-label="{{ __('home.nav.brand') }}">
                        <img src="{{ asset('images/logo/imgi_27_cropped-cropped-Logo-OPM-1-600x427.png') }}"
                            alt="{{ __('home.nav.brand') }}" class="nav-logo-mark" />
                        {{-- <div class="nav-logo-copy">
                            <span>{{ __('home.nav.kicker') }}</span>
                            <strong>{{ $siteName }}</strong>
                        </div> --}}
                    </a>
                    {{-- <div class="nav-status">
                        <span class="nav-status-dot"></span>
                        {{ __('home.nav.status') }}
                    </div> --}}
                </div>

                <ul class="nav-links" role="list">
                    @foreach ($navItems as $item)
                        <li>
                            <a href="{{ $item['href'] }}" class="{{ $item['active'] ? 'is-active' : '' }}"
                                @if ($item['active']) aria-current="page" @endif>{{ $item['label'] }}</a>
                        </li>
                    @endforeach
                </ul>

                <div class="nav-cta">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-ghost">{{ __('admin.nav.user_dashboard') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-ghost">{{ __('admin.auth.login_submit') }}</a>
                    @endauth
                    <a href="{{ $whatsappCtaHref }}" class="btn-primary" target="_blank"
                        rel="noopener">{{ __('home.nav.start_trial') }}</a>
                </div>

                <button class="nav-hamburger" id="hamburger" aria-label="{{ __('home.nav.toggle_menu') }}"
                    aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </nav>

    @if ($showHero)
        <section class="hero" id="hero-top">
            <div class="hero-frame hero-frame-wide reveal">
                <div class="hero-copy">
                    <div class="hero-slider" id="headerSlider">
                        <article class="header-slide is-active" data-slide-index="0" id="hero-services">
                            <span class="header-slide-kicker">{{ __('home.hero.slide_1.kicker') }}</span>
                            <h1 class="header-slide-title">{{ $banner?->titre ?? __('home.hero.slide_1.title') }}</h1>
                            <p class="header-slide-text">{{ $banner?->sous_titre ?? __('home.hero.slide_1.text') }}
                            </p>
                            <div class="hero-actions">
                                <a href="{{ $offersUrl }}" class="btn-primary-lg">
                                    {{ $banner?->bouton1_texte ?? __('home.hero.slide_1.primary') }}
                                    <span class="btn-arrow">-></span>
                                </a>
                                <a href="{{ $heroSecondaryHref }}" class="btn-outline-lg"
                                    @if (str_starts_with($heroSecondaryHref, 'https://wa.me/')) target="_blank" rel="noopener" @endif>
                                    <span>&gt;</span>
                                    {{ $banner?->bouton2_texte ?? __('home.hero.slide_1.secondary') }}
                                </a>
                            </div>
                        </article>

                        <article class="header-slide" data-slide-index="1" id="hero-story">
                            <span class="header-slide-kicker">{{ __('home.hero.slide_2.kicker') }}</span>
                            <h2 class="header-slide-title">{{ __('home.hero.slide_2.title') }}</h2>
                            <p class="header-slide-text">{{ __('home.hero.slide_2.text') }}</p>
                            <div class="hero-actions">
                                <a href="#home-prayer" class="btn-primary-lg">
                                    {{ __('home.hero.slide_2.primary') }}
                                    <span class="btn-arrow">-></span>
                                </a>
                                <a href="mailto:{{ $siteEmail }}" class="btn-outline-lg">
                                    <span>&gt;</span> {{ __('home.hero.slide_2.secondary') }}
                                </a>
                            </div>
                        </article>
                    </div>

                    <div class="header-slider-controls">
                        <button type="button" class="header-slider-arrow" id="headerSlidePrev"
                            aria-label="{{ __('home.hero.controls.prev') }}">
                            <span>&lt;</span>
                        </button>
                        <div class="header-slider-dots" id="headerSlideDots"
                            aria-label="{{ __('home.hero.controls.pagination') }}"></div>
                        <button type="button" class="header-slider-arrow" id="headerSlideNext"
                            aria-label="{{ __('home.hero.controls.next') }}">
                            <span>&gt;</span>
                        </button>
                    </div>
                </div>

                <div class="hero-visual">
                    <div class="hero-slide-panel is-active" data-panel-index="0">
                        <div class="hero-panel-card hero-panel-primary{{ $headerVerseOne ? ' has-verse' : '' }}">
                            <div class="hero-panel-copy">
                                <span class="hero-panel-tag">{{ __('home.hero.slide_1.card_tag') }}</span>
                                <h3>{{ __('home.hero.slide_1.card_title') }}</h3>
                                <p>{{ __('home.hero.slide_1.card_text') }}</p>
                            </div>
                            @if ($headerVerseOne)
                                <div class="hero-verse-snippet">
                                    <div class="hero-verse-head">
                                        <span class="hero-verse-label">{{ __('home.hero.verse_label') }}</span>
                                        <span class="hero-verse-version">{{ $headerVerseOne->version }}</span>
                                    </div>
                                    <div class="hero-verse-body">
                                        <strong class="hero-verse-reference">{{ $headerVerseOne->reference }}</strong>
                                        <p>{{ \Illuminate\Support\Str::limit($headerVerseOne->texte, 135) }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="hero-slide-panel" data-panel-index="1">
                        <div class="hero-panel-card hero-panel-secondary{{ $headerVerseTwo ? ' has-verse' : '' }}">
                            <div class="hero-panel-copy">
                                <span class="hero-panel-tag">{{ __('home.hero.slide_2.card_tag') }}</span>
                                <h3>{{ __('home.hero.slide_2.card_title') }}</h3>
                                <p>{{ __('home.hero.slide_2.card_text') }}</p>
                            </div>
                            @if ($headerVerseTwo)
                                <div class="hero-verse-snippet">
                                    <div class="hero-verse-head">
                                        <span class="hero-verse-label">{{ __('home.hero.verse_label') }}</span>
                                        <span class="hero-verse-version">{{ $headerVerseTwo->version }}</span>
                                    </div>
                                    <div class="hero-verse-body">
                                        <strong class="hero-verse-reference">{{ $headerVerseTwo->reference }}</strong>
                                        <p>{{ \Illuminate\Support\Str::limit($headerVerseTwo->texte, 135) }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{ $slot }}

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="{{ $homeUrl }}" class="nav-logo" aria-label="{{ __('home.nav.brand') }}">
                        <img src="{{ asset('images/logo/imgi_4_LE-TRANSFIGURANT-2.png') }}"
                            alt="{{ __('home.nav.brand') }}" style="height:28px; width:auto; display:block;" />
                    </a>
                    <p class="footer-brand-desc">{{ $siteSlogan ?: __('home.footer.description') }}</p>
                    <div class="footer-contact-meta">
                        <p>{{ __('home.footer.hours') }}: {{ $siteHours }}</p>
                        <p>{{ __('home.footer.location') }}: {{ $siteAddress }}</p>
                    </div>
                    <div class="footer-socials">
                        <a href="#" class="social-btn" aria-label="Twitter / X">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                            </svg>
                        </a>
                        <a href="#" class="social-btn" aria-label="LinkedIn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                            </svg>
                        </a>
                        <a href="#" class="social-btn" aria-label="YouTube">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                            </svg>
                        </a>
                        <a href="#" class="social-btn" aria-label="TikTok">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div>
                    <div class="footer-col-label">{{ __('home.footer.product') }}</div>
                    <div class="footer-links">
                        <a href="{{ $homeUrl }}">{{ __('home.nav.app') }}</a>
                        <a href="{{ $offersUrl }}">{{ __('home.nav.features') }}</a>
                        <a href="{{ $cvServicesUrl }}">{{ __('home.nav.services') }}</a>
                        <a href="{{ $trainingsUrl }}">{{ __('home.nav.trainings') }}</a>
                        <a href="{{ $articlesUrl }}">{{ __('home.nav.blog') }}</a>
                        <a href="{{ $contactPrayerUrl }}">{{ __('home.nav.contact_page') }}</a>
                    </div>
                </div>
                <div>
                    <div class="footer-col-label">{{ __('home.footer.company') }}</div>
                    <div class="footer-links">
                        <a href="#">{{ __('home.footer.about') }}</a>
                        <a href="{{ $articlesUrl }}">{{ __('home.footer.blog') }}</a>
                        <a href="#">{{ __('home.footer.careers') }}</a>
                        <a href="#">{{ __('home.footer.press_kit') }}</a>
                        <a href="#">{{ __('home.footer.status') }}</a>
                    </div>
                </div>
                <div>
                    <div class="footer-col-label">{{ __('home.footer.support') }}</div>
                    <div class="footer-links">
                        <a href="#">{{ __('home.footer.help_center') }}</a>
                        <a href="#">{{ __('home.footer.documentation') }}</a>
                        <a href="#">{{ __('home.footer.security') }}</a>
                        <a href="{{ $contactPrayerUrl }}">{{ __('home.footer.contact') }}</a>
                        <a href="#">{{ __('home.footer.community') }}</a>
                    </div>
                </div>
                <div>
                    <div class="footer-col-label">{{ __('home.footer.newsletter_title') }}</div>
                    <p class="footer-brand-desc">{{ __('home.footer.newsletter_text') }}</p>

                    @if (session('newsletter_success'))
                        <p class="footer-newsletter-success">{{ session('newsletter_success') }}</p>
                    @endif

                    <form method="POST" action="{{ route('newsletter.subscribe') }}" class="footer-newsletter-form">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ url()->full() }}">

                        <input
                            type="text"
                            name="prenom"
                            value="{{ old('prenom') }}"
                            placeholder="{{ __('home.forms.newsletter.prenom') }}"
                            class="footer-newsletter-input" />

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="{{ __('home.forms.newsletter.email') }}"
                            class="footer-newsletter-input"
                            required />

                        @error('email')
                            <small class="footer-newsletter-error">{{ $message }}</small>
                        @enderror

                        <button type="submit" class="btn-primary footer-newsletter-btn">
                            {{ __('home.forms.newsletter.submit') }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="footer-copy">
                    {{ __('home.footer.copyright') }} <a rel="nofollow" href=""
                        target="_blank">@SamuelLarios</a>
                </div>
                <div class="footer-legal">
                    <a href="#">{{ __('home.footer.privacy') }}</a>
                    <a href="#">{{ __('home.footer.terms') }}</a>
                    <a href="#">{{ __('home.footer.cookies') }}</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('templatemo-622-clearwave.js') }}"></script>
    @livewireScripts
</body>

</html>
