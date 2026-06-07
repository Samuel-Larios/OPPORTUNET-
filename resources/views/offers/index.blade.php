@php
    $siteName = $siteName ?? 'Opportunet Mondiale';
    $siteSlogan = $siteSlogan ?? __('home.hero.badge');
    $siteEmail = $siteEmail ?? 'contact@opportunetmondiale.com';
    $siteHours = $siteHours ?? 'Lundi - Samedi 08:00 - 22:00';
    $siteAddress = $siteAddress ?? 'En face de la Mairie de Missérété, Ouémé, BJ';
    $siteWhatsapp = $siteWhatsapp ?? '+2290167229575';
    $siteWhatsappMessage = $siteWhatsappMessage ?? __('home.forms.whatsapp_default');
    $whatsappBase = 'https://wa.me/' . preg_replace('/\D+/', '', $siteWhatsapp ?? '');
    $localizedOffersUrl = \App\Support\Seo::localizedUrl(route('offers.index'), app()->getLocale());
    $seoTitle = app()->getLocale() === 'fr'
        ? 'Offres d emploi au Benin et opportunites internationales'
        : __('offers.meta.title');
    $hasActiveFilters = request()->filled('q')
        || request()->filled('type')
        || request()->filled('contrat')
        || request()->filled('pays')
        || request()->boolean('teletravail')
        || request()->boolean('urgent');
    $seoDescription = \App\Support\Seo::description(__('offers.meta.description') !== 'offers.meta.description'
        ? __('offers.meta.description')
        : __('offers.page.subtitle'));
    $seoKeywords = __('offers.meta.keywords') !== 'offers.meta.keywords' ? __('offers.meta.keywords') : null;
    $seoSchema = [
        \App\Support\Seo::breadcrumb([
            ['name' => $siteName, 'url' => \App\Support\Seo::localizedUrl(route('home'), app()->getLocale())],
            ['name' => __('offers.page.label'), 'url' => $localizedOffersUrl],
        ]),
        \App\Support\Seo::schema('CollectionPage', [
            'name' => __('offers.meta.title'),
            'url' => $localizedOffersUrl,
            'description' => $seoDescription,
            'inLanguage' => app()->getLocale(),
            'about' => [
                ['@type' => 'Thing', 'name' => 'Jobs in Benin'],
                ['@type' => 'Thing', 'name' => 'International opportunities'],
                ['@type' => 'Thing', 'name' => 'Remote work'],
            ],
        ]),
    ];
@endphp

<x-layouts.app
    :title="$seoTitle"
    :description="$seoDescription"
    :keywords="$seoKeywords"
    :canonical="$localizedOffersUrl"
    :robots="$hasActiveFilters ? 'noindex,follow' : 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1'"
    :schema-data="$seoSchema"
    :site-name="$siteName"
    :site-slogan="$siteSlogan"
    :site-email="$siteEmail"
    :site-hours="$siteHours"
    :site-address="$siteAddress"
    :site-whatsapp="$siteWhatsapp"
    :site-whatsapp-message="$siteWhatsappMessage"
    :show-hero="false"
>
    <main class="offers-page">
        <livewire:offers-index :site-email="$siteEmail" />

        <section class="cv-services-list-section offers-services-section" id="offers-services">
            <div class="container">
                <div class="home-section-head reveal">
                    <span class="section-label">{{ __('offers.services.label') }}</span>
                    <h2 class="section-title">{{ __('offers.services.title') }}</h2>
                    <p class="section-sub">{{ __('offers.services.subtitle') }}</p>
                </div>

                <div class="articles-grid">
                    @forelse ($services as $index => $service)
                        @php
                            $accent = $service->category?->couleur ?: '#1A7A6E';
                            $imageUrl = $service->publicImageUrl();
                            $serviceMessage = $service->whatsapp_message ?: __('offers.services.default_whatsapp', ['service' => $service->titre]);
                            $serviceWhatsappHref = $whatsappBase . '?text=' . urlencode($serviceMessage);
                        @endphp
                        <article class="article-card offers-service-card reveal reveal-delay-{{ min($index + 1, 4) }}">
                            <div class="article-card-visual" style="--article-accent: {{ $accent }};">
                                @if ($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $service->titre }}">
                                @else
                                    <div class="article-card-placeholder">
                                        <span>{{ $service->category?->nom ?: __('offers.services.category_fallback') }}</span>
                                        <strong>{{ $service->titre }}</strong>
                                    </div>
                                @endif
                            </div>

                            <div class="article-card-body">
                                <div class="article-card-top">
                                    <div class="article-badges">
                                        <span class="article-category-badge" style="--article-accent: {{ $accent }};">
                                            {{ $service->category?->nom ?: __('offers.services.category_fallback') }}
                                        </span>
                                        @if ($service->en_vedette)
                                            <span class="article-featured-badge">{{ __('offers.services.featured') }}</span>
                                        @endif
                                    </div>

                                    <span class="article-date">{{ __('offers.services.types.' . $service->type) }}</span>
                                </div>

                                <h3>{{ $service->titre }}</h3>
                                <p>{{ $service->description_courte }}</p>

                                <div class="article-meta-list">
                                    <span>{{ $service->duree ?: __('offers.services.duration_fallback') }}</span>
                                    <span>
                                        @if ($service->prix)
                                            {{ number_format((float) $service->prix, 0, ',', ' ') }} {{ $service->devise }}
                                        @else
                                            {{ __('offers.services.on_request') }}
                                        @endif
                                    </span>
                                </div>

                                <div class="article-card-actions">
                                    <a href="{{ $serviceWhatsappHref }}" class="opportunity-link" target="_blank" rel="noopener">
                                        {{ __('offers.services.whatsapp_cta') }}
                                    </a>
                                </div>
                                <x-share-buttons
                                    :url="$localizedOffersUrl . '#offers-services'"
                                    :title="$service->titre"
                                    variant="compact"
                                />
                            </div>
                        </article>
                    @empty
                        <article class="empty-card reveal">
                            <h3>{{ __('offers.services.empty_title') }}</h3>
                            <p>{{ __('offers.services.empty_text') }}</p>
                        </article>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
</x-layouts.app>
