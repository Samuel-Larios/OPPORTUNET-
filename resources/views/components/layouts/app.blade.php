@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'canonical' => null,
    'robots' => 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1',
    'image' => null,
    'type' => 'website',
    'schemaData' => [],
    'pageBannerTitle' => null,
    'siteName' => 'Opportunet Mondiale',
    'siteSlogan' => null,
    'siteEmail' => 'contact@opportunetmondiale.com',
    'siteHours' => 'Lundi - Samedi 08:00 - 22:00',
    'siteAddress' => "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
    'siteWhatsapp' => '+2290167229575',
    'siteWhatsappMessage' => null,
    'socialLinks' => [],
    'banner' => null,
    'headerVerses' => null,
    'featuredVerse' => null,
    'showHero' => true,
])

@php
    $locale = app()->getLocale();
    $homeBaseUrl = route('home');
    $offersBaseUrl = route('offers.index');
    $cvServicesBaseUrl = route('cv.services.index');
    $trainingsBaseUrl = route('trainings.index');
    $articlesBaseUrl = route('articles.index');
    $contactPrayerBaseUrl = route('contact.prayer.index');
    $homeUrl = \App\Support\Seo::localizedUrl($homeBaseUrl, $locale);
    $offersUrl = \App\Support\Seo::localizedUrl($offersBaseUrl, $locale);
    $cvServicesUrl = \App\Support\Seo::localizedUrl($cvServicesBaseUrl, $locale);
    $trainingsUrl = \App\Support\Seo::localizedUrl($trainingsBaseUrl, $locale);
    $articlesUrl = \App\Support\Seo::localizedUrl($articlesBaseUrl, $locale);
    $contactPrayerUrl = \App\Support\Seo::localizedUrl($contactPrayerBaseUrl, $locale);
    $currentUrl = url()->current();
    $titleText = $title ? $title . ' | ' . $siteName : $siteName;
    $metaDescription = \App\Support\Seo::description($description ?: $siteSlogan ?: $siteName);
    $canonicalUrl = $canonical ?: \App\Support\Seo::localizedUrl($currentUrl, $locale, request()->query());
    $alternateLocaleUrls = \App\Support\Seo::alternateLocaleUrls($currentUrl, request()->query());
    $metaImage = \App\Support\Seo::absoluteImageUrl($image ?: 'images/logo/imgi_27_cropped-cropped-Logo-OPM-1-600x427.png');
    $metaKeywords = is_array($keywords)
        ? collect($keywords)->filter()->implode(', ')
        : trim((string) $keywords);
    $pageHeading = trim(strip_tags((string) ($pageBannerTitle ?? $title ?? '')));
    $siteLogo = \App\Support\Seo::absoluteImageUrl('images/logo/imgi_27_cropped-cropped-Logo-OPM-1-600x427.png');
    $socialLinks = array_filter(is_array($socialLinks) ? $socialLinks : []);
    $aboutUrl = \App\Support\Seo::localizedUrl(route('site.about'), $locale);
    $helpUrl = \App\Support\Seo::localizedUrl(route('site.help'), $locale);
    $docsUrl = \App\Support\Seo::localizedUrl(route('site.documentation'), $locale);
    $securityUrl = \App\Support\Seo::localizedUrl(route('site.security'), $locale);
    $communityUrl = \App\Support\Seo::localizedUrl(route('community.testimonials.index'), $locale);
    $privacyUrl = \App\Support\Seo::localizedUrl(route('site.privacy'), $locale);
    $termsUrl = \App\Support\Seo::localizedUrl(route('site.terms'), $locale);
    $cookiesUrl = \App\Support\Seo::localizedUrl(route('site.cookies'), $locale);
    $statusUrl = url('/up');
    $schemaBlocks = array_values(array_filter(array_merge([
        \App\Support\Seo::schema('Organization', [
            'name' => $siteName,
            'url' => $homeUrl,
            'description' => $siteSlogan,
            'email' => $siteEmail,
            'logo' => $siteLogo,
            'address' => $siteAddress ? [
                '@type' => 'PostalAddress',
                'streetAddress' => $siteAddress,
                'addressCountry' => 'BJ',
            ] : null,
            'contactPoint' => [[
                '@type' => 'ContactPoint',
                'contactType' => 'customer support',
                'email' => $siteEmail,
                'telephone' => $siteWhatsapp,
                'availableLanguage' => ['fr', 'en'],
            ]],
        ]),
        \App\Support\Seo::schema('WebSite', [
            'name' => $siteName,
            'url' => $homeUrl,
            'description' => $siteSlogan ?: $metaDescription,
            'inLanguage' => $locale,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => $offersBaseUrl . '?lang=' . $locale . '&q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ]),
    ], is_array($schemaData) ? $schemaData : [])));
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
<html lang="{{ str_replace('_', '-', $locale) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $titleText }}</title>
    <meta name="description" content="{{ $metaDescription }}" />
    <meta name="robots" content="{{ $robots }}" />
    @if ($metaKeywords !== '')
        <meta name="keywords" content="{{ $metaKeywords }}" />
    @endif
    <link rel="canonical" href="{{ $canonicalUrl }}" />
    @foreach ($alternateLocaleUrls as $hrefLang => $href)
        <link rel="alternate" hreflang="{{ $hrefLang }}" href="{{ $href }}" />
    @endforeach

    <meta property="og:type" content="{{ $type }}" />
    <meta property="og:title" content="{{ $titleText }}" />
    <meta property="og:description" content="{{ $metaDescription }}" />
    <meta property="og:url" content="{{ $canonicalUrl }}" />
    <meta property="og:site_name" content="{{ $siteName }}" />
    <meta property="og:locale" content="{{ $locale === 'fr' ? 'fr_FR' : 'en_US' }}" />
    <meta property="og:locale:alternate" content="{{ $locale === 'fr' ? 'en_US' : 'fr_FR' }}" />
    @if ($metaImage)
        <meta property="og:image" content="{{ $metaImage }}" />
        <meta property="og:image:alt" content="{{ $titleText }}" />
    @endif

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $titleText }}" />
    <meta name="twitter:description" content="{{ $metaDescription }}" />
    @if ($metaImage)
        <meta name="twitter:image" content="{{ $metaImage }}" />
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,300&family=Playfair+Display:ital,wght@0,700;1,600&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('templatemo-622-clearwave.css') }}" />
    @livewireStyles

    <link rel="icon" type="image/png" href="{{ asset('images/logo/imgi_4_LE-TRANSFIGURANT-2.png') }}" />
    <link rel="apple-touch-icon" type="image/png" href="{{ asset('images/logo/imgi_4_LE-TRANSFIGURANT-2.png') }}" />
    @foreach ($schemaBlocks as $schemaBlock)
        <script type="application/ld+json">{!! json_encode($schemaBlock, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endforeach
</head>

<body>
    <div class="site-topbar">
        <div class="container site-topbar-inner">
            <div class="site-topbar-left">
                <a href="mailto:{{ $siteEmail }}" class="topbar-link">{{ __('home.topbar.email') }}:
                    {{ $siteEmail }}</a>
                <a href="tel:{{ preg_replace('/\s+/', '', $siteWhatsapp) }}" class="topbar-link">{{ __('home.topbar.phone') }}:
                    {{ $siteWhatsapp }}</a>
                <span class="topbar-link">{{ __('home.topbar.hours') }}: {{ $siteHours }}</span>
            </div>
            <div class="site-topbar-right">
                <span class="topbar-locale-text">{{ __('home.nav.language') }}</span>
                <div class="topbar-locale-switcher" aria-label="{{ __('home.nav.language_switcher') }}">
                    <a href="{{ \App\Support\Seo::localizedUrl(url()->current(), 'fr', request()->query()) }}"
                        class="locale-flag-link{{ app()->getLocale() === 'fr' ? ' active' : '' }}"
                        aria-label="{{ __('home.nav.switch_to_french') }}">
                        <span class="flag-icon flag-fr" aria-hidden="true"></span>
                        <span>FR</span>
                    </a>
                    <a href="{{ \App\Support\Seo::localizedUrl(url()->current(), 'en', request()->query()) }}"
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
                        <div class="nav-logo-copy nav-logo-copy-mobile">
                            <span>{{ __('home.nav.kicker') }}</span>
                            <strong>{{ $siteName }}</strong>
                        </div>
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
    @elseif ($pageHeading !== '')
        <section class="page-banner">
            <div class="page-banner-glow page-banner-glow-one" aria-hidden="true"></div>
            <div class="page-banner-glow page-banner-glow-two" aria-hidden="true"></div>
            <div class="container">
                <div class="page-banner-inner reveal visible">
                    <div class="page-banner-copy">
                        <span class="page-banner-kicker">{{ __('home.nav.kicker') }}</span>
                        <h1 class="page-banner-title">{{ $pageHeading }}</h1>
                        <p class="page-banner-subtitle">{{ $siteSlogan ?: __('home.topbar.focus') }}</p>
                    </div>
                    <div class="page-banner-breadcrumbs" aria-label="{{ __('home.nav.label') }}">
                        <a href="{{ $homeUrl }}">{{ __('home.nav.app') }}</a>
                        <span>/</span>
                        <strong>{{ $pageHeading }}</strong>
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
                            alt="{{ __('home.nav.brand') }}" class="footer-logo-mark" />
                    </a>
                    <p class="footer-brand-desc">{{ $siteSlogan ?: __('home.footer.description') }}</p>
                    <div class="footer-contact-meta">
                        <p>{{ __('home.footer.phone') }}: <a href="tel:{{ preg_replace('/\s+/', '', $siteWhatsapp) }}">{{ $siteWhatsapp }}</a></p>
                        <p>{{ __('home.footer.hours') }}: {{ $siteHours }}</p>
                        <p>{{ __('home.footer.location') }}: {{ $siteAddress }}</p>
                    </div>
                    @if ($socialLinks !== [])
                        <div class="footer-socials">
                            @if (! empty($socialLinks['facebook']))
                                <a href="{{ $socialLinks['facebook'] }}" class="social-btn" aria-label="Facebook" target="_blank" rel="noopener">
                                    <span>Fb</span>
                                </a>
                            @endif
                            @if (! empty($socialLinks['instagram']))
                                <a href="{{ $socialLinks['instagram'] }}" class="social-btn" aria-label="Instagram" target="_blank" rel="noopener">
                                    <span>Ig</span>
                                </a>
                            @endif
                            @if (! empty($socialLinks['linkedin']))
                                <a href="{{ $socialLinks['linkedin'] }}" class="social-btn" aria-label="LinkedIn" target="_blank" rel="noopener">
                                    <span>In</span>
                                </a>
                            @endif
                            @if (! empty($socialLinks['youtube']))
                                <a href="{{ $socialLinks['youtube'] }}" class="social-btn" aria-label="YouTube" target="_blank" rel="noopener">
                                    <span>Yt</span>
                                </a>
                            @endif
                            @if (! empty($socialLinks['tiktok']))
                                <a href="{{ $socialLinks['tiktok'] }}" class="social-btn" aria-label="TikTok" target="_blank" rel="noopener">
                                    <span>Tk</span>
                                </a>
                            @endif
                        </div>
                    @endif
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
                        <a href="{{ $aboutUrl }}">{{ __('home.footer.about') }}</a>
                        <a href="{{ $articlesUrl }}">{{ __('home.footer.blog') }}</a>
                        <a href="{{ $offersUrl }}">{{ __('home.footer.careers') }}</a>
                        <a href="{{ $docsUrl }}">{{ __('home.footer.press_kit') }}</a>
                        <a href="{{ $statusUrl }}">{{ __('home.footer.status') }}</a>
                    </div>
                </div>
                <div>
                    <div class="footer-col-label">{{ __('home.footer.support') }}</div>
                    <div class="footer-links">
                        <a href="{{ $helpUrl }}">{{ __('home.footer.help_center') }}</a>
                        <a href="{{ $docsUrl }}">{{ __('home.footer.documentation') }}</a>
                        <a href="{{ $securityUrl }}">{{ __('home.footer.security') }}</a>
                        <a href="{{ $contactPrayerUrl }}">{{ __('home.footer.contact') }}</a>
                        <a href="{{ $communityUrl }}">{{ __('home.footer.community') }}</a>
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
                        <x-honeypot />
                        <x-form-captcha wrapper-class="footer-captcha-field" error-class="footer-newsletter-error" />
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
                    {{ __('home.footer.copyright') }} <a rel="nofollow" href="{{ $aboutUrl }}">Opportunet Mondiale</a>
                </div>
                <div class="footer-legal">
                    <a href="{{ $privacyUrl }}">{{ __('home.footer.privacy') }}</a>
                    <a href="{{ $termsUrl }}">{{ __('home.footer.terms') }}</a>
                    <a href="{{ $cookiesUrl }}">{{ __('home.footer.cookies') }}</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('templatemo-622-clearwave.js') }}"></script>
    @livewireScripts
</body>

</html>
