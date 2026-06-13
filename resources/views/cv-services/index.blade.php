@php
    $siteName = $siteName ?? 'Opportunet Mondiale';
    $siteSlogan = $siteSlogan ?? __('home.hero.badge');
    $siteEmail = $siteEmail ?? 'contact@opportunetmondiale.com';
    $siteHours = $siteHours ?? 'Lundi - Samedi 08:00 - 22:00';
    $siteAddress = $siteAddress ?? 'En face de la Mairie de Missérété, Ouémé, BJ';
    $siteWhatsapp = $siteWhatsapp ?? '+2290166441840';
    $siteWhatsappMessage = $siteWhatsappMessage ?? __('home.forms.whatsapp_default');
    $whatsappBase = 'https://wa.me/' . preg_replace('/\D+/', '', $siteWhatsapp ?? '');
    $defaultWhatsappHref =
        $whatsappBase . '?text=' . urlencode($siteWhatsappMessage ?? __('home.forms.whatsapp_default'));
    $seoDescription = \App\Support\Seo::description(__('cv_services.page.subtitle'));
    $seoSchema = [
        \App\Support\Seo::breadcrumb([
            ['name' => $siteName, 'url' => \App\Support\Seo::localizedUrl(route('home'), app()->getLocale())],
            [
                'name' => __('cv_services.page.label'),
                'url' => \App\Support\Seo::localizedUrl(route('cv.services.index'), app()->getLocale()),
            ],
        ]),
        \App\Support\Seo::schema('CollectionPage', [
            'name' => __('cv_services.meta.title'),
            'url' => \App\Support\Seo::localizedUrl(route('cv.services.index'), app()->getLocale()),
            'description' => $seoDescription,
            'inLanguage' => app()->getLocale(),
        ]),
    ];
@endphp

<x-layouts.app :title="__('cv_services.meta.title')" :description="$seoDescription" :canonical="\App\Support\Seo::localizedUrl(route('cv.services.index'), app()->getLocale())" :schema-data="$seoSchema" :site-name="$siteName"
    :site-slogan="$siteSlogan" :site-email="$siteEmail" :site-hours="$siteHours" :site-address="$siteAddress" :site-whatsapp="$siteWhatsapp" :site-whatsapp-message="$siteWhatsappMessage"
    :show-hero="false">
    <main class="cv-services-page">
        <section class="cv-services-hero">
            <div class="container">
                <div class="cv-services-hero-shell reveal">
                    <div class="cv-services-hero-copy">
                        <span class="section-label">{{ __('cv_services.page.label') }}</span>
                        <h1 class="section-title">{{ __('cv_services.page.title') }}</h1>
                        <p class="section-sub">{{ __('cv_services.page.subtitle') }}</p>
                    </div>
                    <div class="cv-services-hero-actions">
                        <a href="#cv-form" class="solid-submit">{{ __('cv_services.page.primary') }}</a>
                        <a href="{{ $defaultWhatsappHref }}" class="ghost-submit" target="_blank"
                            rel="noopener">{{ __('cv_services.page.secondary') }}</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="cv-form-section" id="cv-form">
            <div class="container">
                <livewire:cv-depot-form />
            </div>
        </section>

        <section class="cv-services-list-section">
            <div class="container">
                <div class="home-section-head reveal">
                    <span class="section-label">{{ __('cv_services.services.label') }}</span>
                    <h2 class="section-title">{{ __('cv_services.services.title') }}</h2>
                    <p class="section-sub">{{ __('cv_services.services.subtitle') }}</p>
                </div>

                <div class="cv-services-grid">
                    @foreach ($services as $index => $service)
                        @php
                            $serviceMessage =
                                $service->whatsapp_message ?:
                                __('cv_services.whatsapp.default_service', ['service' => $service->titre]);
                            $serviceWhatsappHref = $whatsappBase . '?text=' . urlencode($serviceMessage);
                        @endphp
                        <article
                            class="service-card service-card-{{ ['blue', 'gold', 'teal', 'slate'][$index % 4] }} reveal reveal-delay-{{ min($index + 1, 4) }}">
                            <div class="service-card-top">
                                <span class="service-badge">{{ strtoupper(substr($service->titre, 0, 2)) }}</span>
                                <span
                                    class="service-meta">{{ $service->duree ?: __('cv_services.services.meta_default') }}</span>
                            </div>
                            <h3>{{ $service->titre }}</h3>
                            <p>{{ $service->description_courte }}</p>
                            <div class="cv-service-footer">
                                <strong>
                                    @if ($service->prix)
                                        {{ number_format((float) $service->prix, 0, ',', ' ') }}
                                        {{ $service->devise }}
                                    @else
                                        {{ __('cv_services.services.on_request') }}
                                    @endif
                                </strong>
                                <a href="{{ $serviceWhatsappHref }}" class="opportunity-link" target="_blank"
                                    rel="noopener">{{ __('cv_services.services.whatsapp_cta') }}</a>
                            </div>
                            <x-share-buttons :url="route('cv.services.index')" :title="$service->titre" variant="compact" />
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="cv-whatsapp-section">
            <div class="container">
                <div class="cv-whatsapp-shell reveal">
                    <div>
                        <span class="section-label">{{ __('cv_services.whatsapp.label') }}</span>
                        <h2 class="section-title">{{ __('cv_services.whatsapp.title') }}</h2>
                        <p class="section-sub">{{ __('cv_services.whatsapp.subtitle') }}</p>
                    </div>
                    <a href="{{ $defaultWhatsappHref }}" class="solid-submit" target="_blank"
                        rel="noopener">{{ __('cv_services.whatsapp.cta') }}</a>
                </div>
            </div>
        </section>
    </main>
</x-layouts.app>
